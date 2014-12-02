<?php
namespace Application\BLL;
/**
 * Class Rubric
 * @package Application\BLL
 */
class Rubric extends BLL
{

	public function getByIds(array $rubricIds)
	{
		return $this->application->db->master->selectAll('SELECT * FROM `rubric` WHERE `rubric_id` IN (?)', array(array_values($rubricIds)) , 'rubric_id');
	}
	public function getActiveLinkedPosts()
	{
		$date           = new \DateTime();
		$interval       = new \DateInterval('P' . Posts::POST_ACTIVE_LIFE_DAYS . 'D');
		$currentMonth   = $date->format('Y_m');
		$previousMonth  = $date->sub($interval)->format('Y_m');

		$months[$currentMonth]  = $currentMonth;
		$months[$previousMonth] = $previousMonth;

		$start  = time() - Posts::POST_ACTIVE_LIFE_DAYS * 24 * 60 * 60;
		$postIds = array();
		foreach($months as $month)
		{
			$posts = $this->getDbMaster()->selectAll(
				'SELECT * FROM `rubric_link_' . $month . '` LM
				 INNER JOIN `active_posts` PA ON PA.post_id=LM.post_id AND PA.author_id=LM.author_id
				 ORDER BY `rating` DESC
				', array($start)
			);

			foreach($posts as $post)
			{
				$postIds[] = $post;
			}
		}
		return $postIds;
	}
	public function linkPostToRubricWithWord($phraseId, $postId, $authorId, $pubDate, $rubricId)
	{
		$this->getDbMaster()->query(
			'INSERT INTO rubric_auto_link(rubric_id, post_id, author_id, pub_date, phrase_id) VALUES(?,?,?,?,?)',
			array(
				$rubricId, $postId, $authorId, $pubDate, $phraseId
			)
		);
	}

	public function getPostsAutoLinkedPhrases(array $postsIds, $rubricId)
	{
		list($postsByMonthsPostIds, $postsByMonthsPairs, $values) = $this->application->bll->posts->prepareMultiSelectQuery($postsIds);
		$autoLinks = array();
		foreach ($postsByMonthsPostIds as $month => $data)
		{
			$autoLinks = $this->getDbMaster()->selectAll(
				'SELECT * FROM `rubric_auto_link` RAL JOIN rubric_phrases RP ON RP.phrase_id=RAL.phrase_id WHERE RP.`rubric_id`=? AND `post_id` IN ('. implode(',', $postsByMonthsPostIds[$month]).') AND (`post_id`, `author_id`) IN (' . implode(',', $postsByMonthsPairs[$month]) .')',
				array($rubricId) + $values[$month]['post_ids'] + $values[$month]['ids']
			);
			break;
		}
		return $autoLinks;
	}

	public function getRubricLinkedPosts($rubricId)
	{
		$date           = new \DateTime();
		$interval       = new \DateInterval('P' . Posts::POST_ACTIVE_LIFE_DAYS . 'D');
		$currentMonth   = $date->format('Y_m');
		$previousMonth  = $date->sub($interval)->format('Y_m');

		$months[$currentMonth]  = $currentMonth;
		$months[$previousMonth] = $previousMonth;

		$start  = time() - Posts::POST_ACTIVE_LIFE_DAYS * 24 * 60 * 60;

		foreach($months as $month)
		{
			$links = $this->getDbMaster()->selectAll(
				'SELECT * FROM `rubric_link_' . $month . '` WHERE `pub_date`>? AND `rubric_id` =? ORDER BY `pub_date` DESC',
				array(
					$start,
					$rubricId
				)
			);
		}

		$posts  = $this->application->bll->posts->getPostsByIds($links);
		$this->application->bll->posts->preparePosts($posts);

		return $posts;
	}

	public function getRubricAutoLinkedPosts($rubricId)
	{
		$autoLinks = $this->getDbMaster()->selectAll(
			'SELECT * FROM `rubric_auto_link` WHERE `rubric_id` =?',
			array($rubricId)
		);
		$posts  = $this->application->bll->posts->getPostsByIds($autoLinks);
		$this->application->bll->posts->preparePosts($posts);

		$phrases = $this->getPostsAutoLinkedPhrases($posts, $rubricId);
		foreach($phrases as $phrase)
		{
			$phrasesIds[$phrase['post_id']][$phrase['author_id']][$phrase['phrase_id']] = $phrase;
		}

		foreach($posts as &$post)
		{
			$post['phrases'] = array();
			if(isset($phrasesIds[$post['post_id']][$post['author_id']]))
			{
				$phrases = $phrasesIds[$post['post_id']][$post['author_id']];
				foreach($phrases as $phrase)
				{
					$post['phrases'][$phrase['phrase_id']] = $phrase;
				}
			}
		}
		unset($post);

		return $posts;
	}

	public function getPhrasesCountsByRubricId($rubricId)
	{
		return $this->getDbMaster()->selectAll('SELECT *, RP.*, COUNT(RAL.post_id) as posts_count from rubric_phrases RP LEFT JOIN rubric_auto_link RAL ON
		 RAL.rubric_id=RP.rubric_id
		  AND
		  RAL.phrase_id=RP.phrase_id
		 WHERE RP.rubric_id=? GROUP BY RP.phrase_id', array($rubricId), 'phrase_id');
	}

	public function confirmPostLink($postId, $authorId, $pubTime, $rubricId)
	{
		$month = date('Y_m', $pubTime);
		$this->getDbMaster()->query(
			'REPLACE INTO `rubric_link_' . $month . '` VALUES(?,?,?,?)',
			array(
				$rubricId,
				$postId,
				$authorId,
				$pubTime
			)
		);

		$this->getDbMaster()->query(
			'DELETE FROM `rubric_auto_link` WHERE post_id=? AND author_id=? AND rubric_id=?',
			array(
				$postId,
				$authorId,
				$rubricId
			)
		);
	}

	public function deletePostLink($postId, $authorId, $pubTime, $rubricId)
	{
		$month = date('Y_m', $pubTime);
		$this->getDbMaster()->query(
			'DELETE FROM `rubric_link_' . $month . '`  WHERE post_id=? AND author_id=? AND rubric_id=? ',
			array(
				$postId,
				$authorId,
				$rubricId,
			)
		);
	}

	public function delPhrase($phraseId)
	{
		return $this->getDbMaster()->query(
			'DELETE FROM `rubric_phrases` WHERE `phrase_id` = ?',
			array(
				$phraseId
			)
		);
	}

	public function addPhrase($rubricId, $phrase)
	{
		return $this->getDbMaster()->query(
			'INSERT INTO `rubric_phrases` SET `rubric_id` = ?, phrase=?',
			array(
				$rubricId,
				$phrase
			)
		);
	}

	public function getAll()
	{
		return $this->getDbMaster()->selectAll('SELECT * FROM `rubric` ORDER BY `parent_id`, `position`', array(), 'rubric_id');
	}

	public function getById($rubricId)
	{
		return $this->getDbMaster()->selectRow('SELECT * FROM `rubric` WHERE `rubric_id` = ?', array($rubricId));
	}

	public function restore($rubricId)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET `deleted` = 0 WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
	}

	public function setHiddenWithChilds($rubricId)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET `deleted` = 1 WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
	}

	public function deleteWithChilds($rubricId)
	{
		return $this->getDbMaster()->query(
			'DELETE FROM `rubric` WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
	}

	public function addToParent($parentId, array $data)
	{
		return $this->getDbMaster()->query(
			'INSERT INTO `rubric` SET
				`name`      = ?,
				`title`     = ?,
				`parent_id` = ?',
			array(
				$data['name'],
				$data['title'],
				$parentId
			)
		);
	}

	public function edit($rubricId, array $data)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET
				`name`      = ?,
				`title`     = ?
				WHERE
				`rubric_id` = ?',
			array(
				$data['name'],
				$data['title'],
				$rubricId
			)
		);
	}
}
