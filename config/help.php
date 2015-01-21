<?php
/**
 * 1. Парсим яндекс. Ставим на каждого автора из яндекса таски
 * \Application\BLL\Queue::QUEUE_AUTHOR_UPDATE_INFO  - обновляем инфу автору
 * \Application\Command\Tasks\Worker\Author::methodUpdateInfo()
 * пишем в базу
 * \Application\BLL\Queue::QUEUE_AUTHOR_FETCH_RSS    - парсим посты автора
 *
 *
 * 2. Каждую минуту запускаем команду, которая добавляет в очередь QUEUE_AUTHOR_FETCH_ALL_INFO
 * 200 авторов, которые не обновлялись 7 дней
 * \Application\Command\Tasks\Worker\Author::methodFetchFullInfo - пишет инфу об авторе в базу
 * @todo парсить картинку автора
 *
 * 3. \Application\BLL\Queue::QUEUE_AUTHOR_FETCH_RSS - парим посты автора
 * \Application\Command\Tasks\Worker\Author::methodFetchRss
 * ставим 2 таска  -
 * \Application\BLL\Queue::QUEUE_POSTS_PROCESS_POSTS - записать посты в базу
 * \Application\BLL\Queue::QUEUE_AUTHOR_FETCH_RSS - спарсить этого же автора через неделю
 *
 *
 * @todo - сохранять посты
 * @todo - считать рейтинг постов
 * @todo - считать рейтинг авторов  и ставить таску на перепарс автора исходя из его рейтинга
 * @todo - парсить видео
 * @todo - спрашивать у гугла о видео
 * @todo - парсить картинки из постов
 *
 *
 *
 */
?>
*/1 * * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php tasks-processor run
*/1 * * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php tasks-processor run-workers
1 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php post fetch-yandex
20 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php post rebuild-active-posts
45 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php author calculate-rating
*/15 * * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php author fill-full-update-queue
*/5 * * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php misc fill-video-update-queue
25 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php search generate-xml
35 */1 * * * /usr/bin/indexer ljtop_active_new_index --rotate
45 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php search apply-rubrics
0 */6 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php misc daily
32 */1 * * * cd /home/sites/redesign.lj-top.ru/cron && php run.php search apply-themes