<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

for ($index=0; $index<count($comment); $index++) {

	$log_comment = $comment[$index];
	$comment_id = $log_comment['wr_id'];

	$content = $log_comment['content'];
	$content = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $content);

	if($log_comment['wr_log'] && !$is_admin) { 
		// 로그 (탐색 / 조합 등의 액션 수행)의 흔적이 있을 경우
		// 관리자가 아니면 삭제 불가
		$is_delete = false;
	}
	
	if($log_comment['wr_id'] != $log_comment['wr_id'] && ($log_comment['is_reply'] || $log_comment['is_edit'] || $log_comment['is_del'])) {
		// 답변, 수정, 삭제가 가능할 경우
		// 또한, 본문의 id와 코멘트의 id가 다를 경우 (같을 경우엔 로그의 상단에 있는 컨트롤을 통해 액션 수행이 가능하다)
		$query_string = str_replace("&", "&amp;", $_SERVER['QUERY_STRING']);
		if($w == 'cu') {
			$sql = " select wr_id, wr_content from $write_table where wr_id = '$indexd' and wr_is_comment = '1' ";
			$cmt = sql_fetch($sql);
			$c_wr_content = $cmt['wr_content'];
		}

		$c_reply_href = './board.php?'.$query_string.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w_'.$list_item['wr_id'];
		$c_edit_href = './board.php?'.$query_string.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w_'.$list_item['wr_id'];
	}

	// 캐릭터 정보 출력
	$is_comment_owner = false;
	$comment_owner_front = "";		// 자기 로그 접두문자
	$comment_owner_behind = "";		// 자기 로그 접미문자

	if(!$log_comment['wr_noname']) { 
		// 오너 정보 출력
		$log_comment['name'] = $log_comment['wr_name'];

		if($list_item['mb_id']!='' && $list_item['mb_id'] == $log_comment['mb_id']) { 
			$is_comment_owner = true;
			$comment_owner_front = $owner_front;
			$comment_owner_behind = $owner_behind;
		}
	} else {
		$is_comment_owner = false;
	}
?>


<div class="item-comment" id="c_<?php echo $comment_id ?>">
	<div class="co-header">
		<? // 로그 작성자와 코멘트 작성자가 동일할 경우, class='owner' 를 추가한다. ?>
		<p <?=$is_comment_owner ? ' class="owner"' : ''?>>
			<?=$comment_owner_front?>
			<span><?=$log_comment['name']?></span>
			<?=$comment_owner_behind?>
		</p>
	</div>

	<div class="co-content">
		<div class="original_comment_area">
			<?
				if($log_comment['wr_link1']) {
					// 로그 등록 시 입력한 외부 링크 정보
			?>
				<span class="link-box">
					<? if($log_comment['wr_link1']) { ?>
						<a href="<?=$log_comment['wr_link1']?>" target="_blank" class="link">LINK</a>
					<? } ?>
					<? if($log_comment['wr_link2']) { ?>
						<a href="<?=$log_comment['wr_link2']?>" target="_blank" class="link">LINK</a>
					<? } ?>
				</span>
			<? } 
			// 코멘트 출력 부분
			$log_comment['content'] = autolink($log_comment['content'], $bo_table, $stx); // 자동 링크 및 해시태그, 로그 링크 등 컨트롤 함수
			$log_comment['content'] = emote_ev($log_comment['content']); // 이모티콘 출력 함수
			echo $log_comment['content'];
			?>
		</div>
		<? if($log_comment['is_edit']) { ?>
		<div class="modify_area" id="save_comment_<?php echo $comment_id ?>">
			<textarea id="save_co_comment_<?php echo $comment_id ?>"><?php echo get_text($log_comment['content1'], 0) ?></textarea>
			<button type="button" class="mod_comment ui-btn" onclick="modify_commnet('<?php echo $comment_id ?>'); return false;">수정</button>
		</div>
		<? } ?>
	</div>

	<div class="co-footer">
		<span class="date">
			<?=date('Y-m-d H:i:s', strtotime($log_comment['wr_datetime']))?>
		</span>
		<?php if ($log_comment['is_del'])  { ?><a href="<?php echo $log_comment['del_link'];  ?>" onclick="return comment_delete();" class="del">삭제</a><?php } ?>
		<?php if ($log_comment['is_edit']) { ?><a href="<?php echo $c_edit_href;  ?>" onclick="comment_box('<?php echo $comment_id ?>', '<?=$list_item['wr_id']?>'); return false;" class="mod">수정</a><?php } ?>
	</div>
</div>
<? } ?>


