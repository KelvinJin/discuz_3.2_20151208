<?php 
  $waittime = interval_check('post');
  if($waittime > 0) {
    showmessage('message_can_not_send_2', '', array(), array('return' => true));
  }

  cknewuser();

  if(!checkperm('allowsendpm')) {
    showmessage('no_privilege_sendpm', '', array(), array('return' => true));
  }

  if($touid) {
    if(isblacklist($touid)) {
      showmessage('is_blacklist', '', array(), array('return' => true));
    }
  }

  if(submitcheck('pmsubmit')) {
    if(!empty($_POST['username'])) {
      $_POST['users'][] = $_POST['username'];
    }
    $users = empty($_POST['users']) ? array() : $_POST['users'];
    $type = intval($_POST['type']);
    $coef = 1;
    if(!empty($users)) {
      $coef = count($users);
    }

    !($_G['group']['exempt'] & 1) && checklowerlimit('sendpm', 0, $coef);

    $message = (!empty($_POST['messageappend']) ? 
      $_POST['messageappend']."\n" : '').trim($_POST['message']);
    
    if(empty($message)) {
      showmessage('unable_to_send_air_news', '', array(), array('return' => true));
    }

    $message = censor($message);
    loadcache(array('smilies', 'smileytypes'));
    foreach($_G['cache']['smilies']['replacearray'] AS $key => $smiley) {
      $_G['cache']['smilies']['replacearray'][$key] = '[img]'.$_G['siteurl']
        .'static/image/smiley/'
        .$_G['cache']['smileytypes'][$_G['cache']['smilies']['typearray'][$key]]['directory'].
        '/'.$smiley.'[/img]';
    }
    $message = preg_replace($_G['cache']['smilies']['searcharray'], 
      $_G['cache']['smilies']['replacearray'], $message);
    $subject = '';
    if($type == 1) {
      $subject = dhtmlspecialchars(trim($_POST['subject']));
    }

    include_once libfile('function/friend');
    $return = 0;
    if($touid || $pmid) {
      if($touid) {
        if(($value = getuserbyuid($touid))) {
          $value['onlyacceptfriendpm'] = $value['onlyacceptfriendpm'] ? 
          $value['onlyacceptfriendpm'] : 
          ($_G['setting']['onlyacceptfriendpm'] ? 1 : 2);
          if($_G['group']['allowsendallpm'] || $value['onlyacceptfriendpm'] == 2 
            || ($value['onlyacceptfriendpm'] == 1 && friend_check($touid))) {
            $return = sendpm($touid, $subject, $message, '', 0, 0, $type);
          } else {
            showmessage('message_can_not_send_onlyfriend', '', 
              array(), array('return' => true));
          }
        } else {
          showmessage('message_bad_touid', '', array(), array('return' => true));
        }
      } else {
        $topmuid = intval($_GET['topmuid']);
        $return = sendpm($topmuid, $subject, $message, '', $pmid, 0);
      }

    } elseif($users) {
      $newusers = $uidsarr = $membersarr = array();
      if($users) {
        $membersarr = C::t('common_member')->fetch_all_by_username($users);
        foreach($membersarr as $aUsername=>$aUser) {
          $uidsarr[] = $aUser['uid'];
        }
      }
      if(empty($membersarr)) {
        showmessage('message_bad_touser', '', array(), array('return' => true));
      }
      if(isset($membersarr[$_G['uid']])) {
        showmessage('message_can_not_send_to_self', '', 
          array(), array('return' => true));
      }

      friend_check($uidsarr);

      foreach($membersarr as $key => $value) {

        $value['onlyacceptfriendpm'] = $value['onlyacceptfriendpm'] 
        ? $value['onlyacceptfriendpm'] : ($_G['setting']['onlyacceptfriendpm'] 
          ? 1 : 2);
        if($_G['group']['allowsendallpm'] || $value['onlyacceptfriendpm'] == 2 
          || ($value['onlyacceptfriendpm'] == 1 
            && $_G['home_friend_'.$value['uid'].'_'.$_G['uid']])) {
          $newusers[$value['uid']] = $value['username'];
          unset($users[array_search($value['username'], $users)]);
        }
      }

      if(empty($newusers)) {
        showmessage('message_can_not_send_onlyfriend', '', array(), 
          array('return' => true));
      }

      foreach($newusers as $key=>$value) {
        if(isblacklist($key)) {
          showmessage('is_blacklist', '', array(), array('return' => true));
        }
      }
      $coef = count($newusers);
      $return = sendpm(implode(',', $newusers), $subject, $message, '', 
        0, 1, $type);
    } else {
      showmessage('message_can_not_send_9', '', array(), array('return' => true));
    }

    if($return > 0) {
      include_once libfile('function/stat');
      updatestat('sendpm', 0, $coef);

      C::t('common_member_status')->update($_G['uid'], 
        array('lastpost' => TIMESTAMP));
      !($_G['group']['exempt'] & 1) && updatecreditbyaction('sendpm', 
        0, array(), '', $coef);
      if(!empty($newusers)) {
        if($type == 1) {
          $returnurl = 'home.php?mod=space&do=pm&filter=privatepm';
        } else {
          $returnurl = 'home.php?mod=space&do=pm';
        }
        showmessage(count($users) ? 'message_send_result' : 'do_success', 
          $returnurl, array('users' => implode(',', $users), 
            'succeed' => count($newusers)));
      } else {
        if(!defined('IN_MOBILE')) {
          showmessage('do_success', 'home.php?mod=space&do=pm&subop=view&touid='
            .$touid, array('pmid' => $return), $_G['inajax'] ? 
            array('msgtype' => 3, 'showmsg' => false) : array());
        } else {
          showmessage('do_success', 'home.php?mod=space&do=pm&subop=view'
            .(intval($_POST['touid']) ? '&touid='.intval($_POST['touid']) 
              : ( intval($_POST['plid']) ? '&plid='.intval($_POST['plid'])
                .'&daterange=1&type=1' : '' )));
        }

      }
    } else {
      if(in_array($return, range(-16, -1))) {
        showmessage('message_can_not_send_'.abs($return));
      } else {
        showmessage('message_can_not_send', '', array(), array('return' => true));
      }
    }
  }