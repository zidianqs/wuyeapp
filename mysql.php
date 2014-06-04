<?php
$mysql_server_name='localhost'; //数据库服务器名称
$mysql_username='root'; // 连接数据库用户名
$mysql_password='root'; // 连接数据库密码
$mysql_database='wuyeapp'; // 数据库的名字

$conn=mysql_connect($mysql_server_name, $mysql_username, $mysql_password);

$strsql='SELECT * FROM `test`';

$result=mysql_db_query($mysql_database, $strsql, $conn);

$row=mysql_fetch_row($result);

echo '<table><tr>';
for ($i=0; $i<mysql_num_fields($result); $i++) {
	echo '<th>' . mysql_field_name($result, $i).'</th>';
}
echo '</tr>';

mysql_data_seek($result, 0);
while ($row=mysql_fetch_row($result)) {
	echo '<tr>';
	for ($i=0; $i<mysql_num_fields($result); $i++ ) {
		echo '<td>'.$row[$i].'</td>';
	}
	echo '</tr>';
}

echo '</table>';

?>