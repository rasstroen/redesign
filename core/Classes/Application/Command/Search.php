<?php
namespace Application\Command;


class Search extends Base
{

	public function actionApplyRubrics()
	{

		$rubrics = $this->application->bll->rubric->getAll();
		$this->application->db->master->query('TRUNCATE rubric_auto_link');
		foreach($rubrics as $rubric)
		{
			$this->log('searching for post to bind to "' . $rubric['name'] . '"');
			$stopWords = array(
				1=>'Владимир Путин',
				2=>'Дмитрий Медведев',
			);

			$cl = new \SphinxClient();
			$cl->SetMatchMode(SPH_MATCH_ANY);
			$cl->SetSortMode(SPH_SORT_RELEVANCE);
			$cl->SetLimits(0, 100, 5000);
			$posts = array();
			foreach($stopWords as $phraseId => $phrase)
			{
				$q = array();
				$words = explode(" ", $phrase);
				foreach($words as $word)
				{
					$w = trim($cl->EscapeString($word));
					$q[] = '(' . $w . ' | *' . $w . '*)';
				}
				$q = implode(' & ', $q);
				$this->log($q);
				$result = $cl->Query($q, 'ljtop_active_new_index');

				foreach($result['matches'] as $match)
				{
					$weight     = $match['weight'];
					$postId     = $match['attrs']['post_id'];
					$authorId   = $match['attrs']['author_id'];
					$pubDate    = $match['attrs']['pub_date'];
					$key = $phraseId . '-' . $postId . '-' . $authorId;
					if(!isset($posts[$key]))
					{
						$posts[$key] = array
						(
							'post_id'   =>$postId,
							'author_id' =>$authorId,
							'pub_time'  =>$pubDate,
							'weight'    =>$weight
						);
						$this->application->bll->rubric->linkPostToRubricWithWord(
							$phraseId,
							$postId,
							$authorId,
							$pubDate,
							$rubric['rubric_id']
						);
					}

					if(count($posts) > 4)
					{
						break;
					}
				}

			}

		}
	}

	public function actionGenerateXml()
	{
		$this->log('XML GEN');
		$this->f = fopen("/tmp/tmpXml", "w");
		$this->_head();
		$step = 200;
		$i=0;
		while($posts = $this->application->bll->posts->getPopularPostsWithOffset($i++ * $step, 200))
		{
			$this->log(count($posts));
			foreach ($posts as $post) {
				$this->_item($post);
			}
		}

		$this->_end();
		fclose($this->f);
		$dest = '/home/sites/redesign.lj-top.ru/xml/ljtop_active.xml';
		$this->log($dest);
		exec('cp /tmp/tmpXml '. $dest);
	}

	private function _item($post) {
		/* @var $post Post */
		ob_start();
		?>
		<sphinx:document id="<?= $post['post_id'] . '000' . $post['author_id'] ?>">
			<title><![CDATA[<?= iconv("UTF-8", "UTF-8//IGNORE", $post['title']) ?>]]></title>
			<pub_date><![CDATA[<?= $post['pub_time'] ?>]]></pub_date>
			<text><![CDATA[<?= iconv("UTF-8", "UTF-8//IGNORE",  $post['title']).' '.strip_tags(iconv("UTF-8", "UTF-8//IGNORE", $post['text'])) ?>]]></text>
			<author_name><![CDATA[<?=  $post['author']['username'] ?>]]></author_name>
			<post_id><?= $post['post_id'] ?></post_id>
			<author_id><?= $post['author_id'] ?></author_id>
		</sphinx:document>
		<?php
		fwrite($this->f, ob_get_clean());
	}

	private function _head() {
		ob_start();
		echo '<?xml version="1.0" encoding="utf-8"?>';
		?><sphinx:docset>
		<sphinx:schema>
			<sphinx:field name="title"/>
			<sphinx:field name="text"/>
			<sphinx:field name="author_name"/>
			<sphinx:attr name="post_id" type="int" bits="32" default="0"/>
			<sphinx:attr name="pub_date" type="int" bits="32" default="0"/>
			<sphinx:attr name="author_id" type="int" bits="32" default="0"/>
		</sphinx:schema><?php
		fwrite($this->f, ob_get_clean());
	}

	private function _end() {
		ob_start();
		?><sphinx:killlist></sphinx:killlist>
		</sphinx:docset><?php
		fwrite($this->f, ob_get_clean());
	}
}