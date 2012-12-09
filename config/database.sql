# we have to set unique key by database.sql because DCA config does not support keys with multiple columns
CREATE TABLE `tl_cloud_node` (
  UNIQUE KEY `cloudapi_path` (`cloudapi`, `path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;