<?php
namespace Application\Command\Tasks\Worker;

class Post extends Base
{
	private $minPostsComments   = 30;
	private $maxPostAge         = 604800; // week
	/**
	 * @param $posts
	 */
	public function methodProcess($data)
	{
		$username = $data['username'];
		$this->log('saving ' . count($data['posts']) . ' posts, author: ' . $username);
		foreach($data['posts'] as $post)
		{
			if($post['comments'] > $this->minPostsComments)
			{
				if(strtotime($post['pubdate']) > time() - $this->maxPostAge)
				{
					/**
					 * Считаем скорость добавления комментариев поста (штук в час) по предыдущему сохранению
					 */

					/**
					 * Считаем рейтинг поста
					 */

					/**
					 * Сохраняем данные поста в его таблицу, зашарденную по месяцу
					 */

					/**
					 * Сохраняем теги поста в таблицу тегов месяца
					 */

					/**
					 * Сохраняем теги поста в таблицу тегов активных постов
					 */

					/**
					 * Сохраняем пост в активные посты
					 */

					/**
					 * Ставим таск на вытащить картинку из поста
					 */

					/**
					 * Ставим таск на вытащить видео из поста
					 */

					/**
					 * Сохраняем связь поста с автором
					 */
				}
			}
			/**
			 * [24] => Array
			(
			[url] => http://duzer007.livejournal.com/1606741.html
			[guid] => http://duzer007.livejournal.com/1606741.html
			[pubdate] => Thu, 13 Nov 2014 14:17:18 GMT
			[title] => А я с этим живу.
			[link] => http://duzer007.livejournal.com/1606741.html
			[description] => Итак, у меня во френдах имеются:тульский ватник, алкоголики из Москвы, имперский выскочка, лиса, одесский укроп, еноты, хуй какой-то и жена евоная, мох, сибаритки и богодулка (в одном лице), теребонька, и это только начало списка...<a name='cutid1-end'></a>
			[comments] => 96
			[tags] => Array
			(
				[френды] => френды
				[подумалось] => подумалось
				[знайнаших] => знайнаших
				[ЖЖ] => ЖЖ
				[дети-радиации] => дети-радиации
			)

			)

			 */
		}
	}
}
