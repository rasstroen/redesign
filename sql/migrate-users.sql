INSERT INTO `author` (
`author_id`,
`is_community`,
`username`,
`userinfo_change_time`,
`has_picture`,
`journal_title` ,
`journal_subtitle` ,
`journal_country_code`,
`journal_city_name`,
`journal_posted` ,
`journal_created` ,
`userinfo_full_change_time`
)
(
SELECT
  `id`,
  `is_group`,
  `name`,
  `last_updated`,
  `has_pic`,
  `title`,
  `description`,
  `country`,
  `city`,
  `posted`,
  `created`,
  `xml_last_fetch`
FROM ljtop.`author`
);