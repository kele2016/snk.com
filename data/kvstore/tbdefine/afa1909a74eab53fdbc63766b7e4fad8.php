<?php exit(); ?>a:3:{s:5:"value";a:8:{s:7:"columns";a:20:{s:7:"item_id";a:6:{s:4:"type";s:6:"number";s:8:"required";b:1;s:4:"pkey";b:1;s:5:"extra";s:14:"auto_increment";s:7:"comment";s:14:"订单明细ID";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:8:"order_id";a:5:{s:4:"type";s:12:"table:orders";s:8:"required";b:1;s:7:"default";i:0;s:7:"comment";s:8:"订单ID";s:8:"realtype";s:19:"bigint(20) unsigned";}s:10:"product_id";a:5:{s:4:"type";s:14:"table:products";s:8:"required";b:1;s:7:"default";i:0;s:7:"comment";s:8:"货品ID";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:8:"goods_id";a:5:{s:4:"type";s:11:"table:goods";s:8:"required";b:1;s:7:"default";i:0;s:7:"comment";s:8:"商品ID";s:8:"realtype";s:19:"bigint(20) unsigned";}s:2:"bn";a:4:{s:4:"type";s:11:"varchar(40)";s:8:"is_title";b:1;s:7:"comment";s:18:"明细商品货号";s:8:"realtype";s:11:"varchar(40)";}s:4:"name";a:3:{s:4:"type";s:12:"varchar(200)";s:7:"comment";s:21:"明细商品的名称";s:8:"realtype";s:12:"varchar(200)";}s:9:"spec_info";a:3:{s:4:"type";s:12:"varchar(200)";s:7:"comment";s:18:"商品规格描述";s:8:"realtype";s:12:"varchar(200)";}s:8:"image_id";a:5:{s:4:"type";s:17:"table:image@image";s:8:"required";b:1;s:7:"default";i:0;s:7:"comment";s:8:"图片ID";s:8:"realtype";s:8:"char(32)";}s:4:"cost";a:3:{s:4:"type";s:5:"money";s:7:"comment";s:21:"明细商品的成本";s:8:"realtype";s:13:"decimal(20,3)";}s:5:"price";a:5:{s:4:"type";s:5:"money";s:7:"default";s:1:"0";s:8:"required";b:1;s:7:"comment";s:9:"销售价";s:8:"realtype";s:13:"decimal(20,3)";}s:15:"member_lv_price";a:5:{s:4:"type";s:5:"money";s:7:"default";s:1:"0";s:8:"required";b:1;s:7:"comment";s:9:"会员价";s:8:"realtype";s:13:"decimal(20,3)";}s:9:"buy_price";a:5:{s:4:"type";s:5:"money";s:7:"default";s:1:"0";s:8:"required";b:1;s:7:"comment";s:9:"成交价";s:8:"realtype";s:13:"decimal(20,3)";}s:6:"amount";a:3:{s:4:"type";s:5:"money";s:7:"comment";s:36:"明细商品总额(成交价x数量)";s:8:"realtype";s:13:"decimal(20,3)";}s:5:"score";a:5:{s:4:"type";s:6:"number";s:5:"label";s:6:"积分";s:5:"width";i:30;s:7:"comment";s:18:"明细商品积分";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:6:"weight";a:3:{s:4:"type";s:6:"number";s:7:"comment";s:18:"明细商品重量";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:4:"nums";a:5:{s:4:"type";s:5:"float";s:7:"default";i:1;s:8:"required";b:1;s:7:"comment";s:24:"明细商品购买数量";s:8:"realtype";s:5:"float";}s:7:"sendnum";a:5:{s:4:"type";s:5:"float";s:7:"default";i:0;s:8:"required";b:1;s:7:"comment";s:24:"明细商品发货数量";s:8:"realtype";s:5:"float";}s:5:"addon";a:3:{s:4:"type";s:8:"longtext";s:7:"comment";s:27:"明细商品的规格属性";s:8:"realtype";s:8:"longtext";}s:9:"item_type";a:5:{s:4:"type";a:4:{s:7:"product";s:6:"商品";s:3:"pkg";s:12:"捆绑商品";s:4:"gift";s:12:"赠品商品";s:7:"adjunct";s:12:"配件商品";}s:7:"default";s:7:"product";s:8:"required";b:1;s:7:"comment";s:18:"明细商品类型";s:8:"realtype";s:38:"enum('product','pkg','gift','adjunct')";}s:9:"seller_id";a:8:{s:4:"type";s:6:"number";s:5:"label";s:6:"商家";s:7:"comment";s:6:"商家";s:7:"default";i:0;s:5:"width";i:110;s:7:"in_list";b:1;s:7:"orderby";b:1;s:8:"realtype";s:21:"mediumint(8) unsigned";}}s:5:"index";a:1:{s:11:"ind_item_bn";a:2:{s:7:"columns";a:1:{i:0;s:2:"bn";}s:4:"type";s:4:"hash";}}s:6:"engine";s:6:"innodb";s:7:"comment";s:15:"订单明细表";s:8:"idColumn";s:7:"item_id";s:5:"pkeys";a:1:{s:7:"item_id";s:7:"item_id";}s:10:"textColumn";s:2:"bn";s:7:"in_list";a:1:{i:0;s:9:"seller_id";}}s:3:"ttl";i:0;s:8:"dateline";i:1441007285;}