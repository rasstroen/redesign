<?php

/* FETCH PUBLIC IMAGES */
require_once 'Cron.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

class CPublicFetcher extends Cron {

     function glue() {
        $time_glue = 30;
        $query = 'SELECT created,pid,gid FROM feed WHERE `created`>' . (time() - 24 * 60 * 60) . ' AND `parent`=0 ORDER BY created DESC';
        $feed = Database::sql2array($query);
        foreach ($feed as $item) {

            if (isset($old[$item['gid']])) {
                if (abs($old[$item['gid']]['created'] - $item['created']) < $time_glue) {
                    $q = 'UPDATE feed SET parent=' . $old[$item['gid']]['pid'] . ' WHERE `pid`=' . $item['pid'] . ' AND `gid`=' . $item['gid'];
                    Database::query($q);
                } else {
                    $old[$item['gid']] = $item;
                }
            }else
                $old[$item['gid']] = $item;
        }
    }

    function work() {
        $this->log('CPublicFetcher');
        $query = 'SELECT * from `public` WHERE `refreshtime`<' . (time() - 5 * 60) . '  ORDER BY `refreshtime`';
        $publics = Database::sql2array($query);
        $this->log(count($publics) . ' will be checked');
        foreach ($publics as $row) {
            $per_query = $row['per_query'] ? $row['per_query'] : 200;
            $offset = $row['offset'];
            $offset-=$per_query * 2;
            $offset = max(0, $offset);
            $this->log('PUBLIC#' . $row['id'] . " OFFSET#$offset pq=$per_query");

            $res = $this->getPublicNews($row['id'], $offset, $per_query);
            if ($res === -1) {
                $offset+=$per_query / 2;
                Database::query('UPDATE public SET per_query=20,offset=' . $offset . ' WHERE id=' . $row['id']);
            }
            echo count($res) . "\n";
            while (count($res) > 0) {
                $offset+=count($res);
                echo date('Y-m-d H:i:s') . " PUBLIC#" . $row['id'] . " OFFSET#$offset COUNT#" . count($res) . "\n";
                Database::query('UPDATE public SET per_query=20,offset=' . ($offset) . ' WHERE id=' . $row['id']);
                $res = $this->getPublicNews($row['id'], $offset, $per_query);
            }
            Database::query('UPDATE `public` SET refreshtime=' . time() . ' WHERE `id`=' . $row['id']);
        }
        $this->log('GLUE');
        $this->glue();
$this->log('END GLUE');
    }

    function getPublicNews($gid = 26406986, $offset = 0, $limit = 200, $aid = 'wall') {
        $api_id = 2727212; // Insert here id of your application
        $secret_key = '6iwNXO1Y68v0jnIg9tmC'; // Insert here secret key of your application
        $VK = new vkapi($api_id, $secret_key);
        $resp = $VK->api('photos.get', array('extended' => 1, 'gid' => $gid, 'aid' => $aid, 'limit' => $limit, 'offset' => $offset));
        if ($resp === -1) {
            return -1;
        }
        $last_post = 0;

        if (!isset($resp['response'])) {
            $this->log('FUCK');
print_r($resp);
        }

        $aaid = (int) $aid;

        foreach ($resp['response'] as $photo) {
            $photo['src_xxbig'] = isset($photo['src_xxbig']) ? $photo['src_xxbig'] : '';
            $photo['src_xbig'] = isset($photo['src_xbig']) ? $photo['src_xbig'] : '';
            $photo['width'] = isset($photo['width']) ? $photo['width'] : '';
            $photo['height'] = isset($photo['height']) ? $photo['height'] : '';
            $this->log($photo['aid'] . "=photo_aid\t" . $aid . "=aid_param\t" . $gid . "=gid " . date('Y-m-d H:i:s', $photo['created']) . "\n");

            $last_post = max($last_post, $photo['created']);

            if ($photo['created'] > (time() - 24 * 60 * 60 * 31)) {
                $q = 'INSERT INTO `feed` SET
                `gid`=' . $gid . ',
                `aid`=' . $aaid . ',
                `pid`=' . $photo['pid'] . ',
                `user_id`=' . $photo['user_id'] . ',
                `text`=' . Database::escape($photo['text']) . ',
                `src`=\'' . $photo['src'] . '\',
                `src_big`=\'' . $photo['src_big'] . '\',
                `src_xbig`=\'' . $photo['src_xbig'] . '\',
                `src_xxbig`=\'' . $photo['src_xxbig'] . '\',
                `src_small`=\'' . $photo['src_small'] . '\',
                `width`=\'' . $photo['width'] . '\',
                `heigth`=\'' . $photo['height'] . '\',
                `likes`=' . $photo['likes']['count'] . ',
                `created`=\'' . $photo['created'] . '\'
                    ON DUPLICATE KEY UPDATE
                `gid`=' . $gid . ',
                `aid`=' . $aaid . ',
                `text`=' . Database::escape($photo['text']) . ',
                `likes`=' . $photo['likes']['count'] . ',
                `src`=\'' . $photo['src'] . '\',
                `width`=\'' . $photo['width'] . '\',
                `heigth`=\'' . $photo['height'] . '\',
                `src_big`=\'' . $photo['src_big'] . '\',
                `src_xbig`=\'' . $photo['src_xbig'] . '\',
                `src_xxbig`=\'' . $photo['src_xxbig'] . '\',
                `src_small`=\'' . $photo['src_small'] . '\'
                ';
                Database::query($q);
            }
        }
        if ($last_post) {
            echo "LAST $last_post \n";
            Database::query('UPDATE `public` SET `last_post`=\'' . date('Y-m-d H:i:s', $last_post) . '\' WHERE id=' . $gid . ' AND last_post<\'' . date('Y-m-d H:i:s', $last_post) . '\'');
            if ($aid != 'wall')
                Database::query('UPDATE `public_albums` SET `last_image`=\'' . date('Y-m-d H:i:s', $last_post) . '\' WHERE album_id=' . $aid . ' AND public_id=' . $gid . ' AND last_image<\'' . date('Y-m-d H:i:s', $last_post) . '\'');
        }


        return $resp['response'];
    }

}

new CPublicFetcher();
