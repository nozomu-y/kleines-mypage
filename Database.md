```
mysql> SHOW TABLES;
+-------------------------------+
| Tables_in_kleines_mypage      |
+-------------------------------+
| accounting_lists              |
| accounting_records            |
| admins                        |
| identity_verifications        |
| individual_accounting_lists   |
| individual_accounting_records |
| login_histories               |
| password_updates              |
| profiles                      |
| users                         |
+-------------------------------+
```

```
mysql> DESC accounting_lists
+---------------+--------------------------+------+-----+---------+----------------+
| Field         | Type                     | Null | Key | Default | Extra          |
+---------------+--------------------------+------+-----+---------+----------------+
| accounting_id | int(5) unsigned zerofill | NO   | PRI | NULL    | auto_increment |
| name          | varchar(256)             | YES  |     | NULL    |                |
| deadline      | date                     | YES  |     | NULL    |                |
| admin         | varchar(32)              | YES  |     | NULL    |                |
+---------------+--------------------------+------+-----+---------+----------------+
```

```
mysql> DESC accounting_records;
+---------------+--------------------------+------+-----+---------+-------+
| Field         | Type                     | Null | Key | Default | Extra |
+---------------+--------------------------+------+-----+---------+-------+
| accounting_id | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| user_id       | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| price         | int(10)                  | YES  |     | NULL    |       |
| paid_cash     | int(10)                  | YES  |     | NULL    |       |
| datetime      | datetime                 | YES  |     | NULL    |       |
+---------------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC admins;
+---------+--------------------------+------+-----+---------+-------+
| Field   | Type                     | Null | Key | Default | Extra |
+---------+--------------------------+------+-----+---------+-------+
| user_id | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| role    | varchar(32)              | NO   | PRI | NULL    |       |
+---------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC identity_verifications;
+----------+--------------------------+------+-----+---------+-------+
| Field    | Type                     | Null | Key | Default | Extra |
+----------+--------------------------+------+-----+---------+-------+
| user_id  | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| datetime | datetime                 | YES  |     | NULL    |       |
| token    | varchar(64)              | YES  |     | NULL    |       |
+----------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC individual_accounting_lists;
+--------------------------+--------------------------+------+-----+---------+----------------+
| Field                    | Type                     | Null | Key | Default | Extra          |
+--------------------------+--------------------------+------+-----+---------+----------------+
| individual_accounting_id | int(5) unsigned zerofill | NO   | PRI | NULL    | auto_increment |
| name                     | varchar(256)             | YES  |     | NULL    |                |
| datetime                 | datetime                 | YES  |     | NULL    |                |
| price                    | int(10)                  | YES  |     | NULL    |                |
+--------------------------+--------------------------+------+-----+---------+----------------+
```

```
mysql> DESC individual_accounting_records;
+--------------------------+--------------------------+------+-----+---------+-------+
| Field                    | Type                     | Null | Key | Default | Extra |
+--------------------------+--------------------------+------+-----+---------+-------+
| user_id                  | int(5) unsigned zerofill | YES  |     | NULL    |       |
| datetime                 | datetime                 | YES  |     | NULL    |       |
| name                     | varchar(256)             | YES  |     | NULL    |       |
| price                    | int(10)                  | YES  |     | NULL    |       |
| accounting_id            | int(5) unsigned zerofill | YES  |     | NULL    |       |
| individual_accounting_id | int(5) unsigned zerofill | YES  |     | NULL    |       |
+--------------------------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC login_histories;
+----------+--------------------------+------+-----+---------+-------+
| Field    | Type                     | Null | Key | Default | Extra |
+----------+--------------------------+------+-----+---------+-------+
| user_id  | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| datetime | datetime                 | NO   | PRI | NULL    |       |
| success  | int(1)                   | YES  |     | NULL    |       |
| IP       | varchar(32)              | YES  |     | NULL    |       |
+----------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC password_updates;
+----------+--------------------------+------+-----+---------+-------+
| Field    | Type                     | Null | Key | Default | Extra |
+----------+--------------------------+------+-----+---------+-------+
| user_id  | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| datetime | datetime                 | NO   | PRI | NULL    |       |
| IP       | varchar(32)              | YES  |     | NULL    |       |
+----------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC profiles;
+------------+--------------------------+------+-----+---------+-------+
| Field      | Type                     | Null | Key | Default | Extra |
+------------+--------------------------+------+-----+---------+-------+
| user_id    | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
| last_name  | varchar(256)             | YES  |     | NULL    |       |
| first_name | varchar(256)             | YES  |     | NULL    |       |
| name_kana  | varchar(256)             | YES  |     | NULL    |       |
| grade      | int(2) unsigned zerofill | YES  |     | NULL    |       |
| part       | varchar(1)               | YES  |     | NULL    |       |
| birthday   | date                     | YES  |     | NULL    |       |
+------------+--------------------------+------+-----+---------+-------+
```

```
mysql> DESC users;
+----------+--------------------------+------+-----+---------+----------------+
| Field    | Type                     | Null | Key | Default | Extra          |
+----------+--------------------------+------+-----+---------+----------------+
| user_id  | int(5) unsigned zerofill | NO   | PRI | NULL    | auto_increment |
| email    | varchar(256)             | YES  | UNI | NULL    |                |
| password | varchar(256)             | YES  |     | NULL    |                |
| status   | varchar(32)              | YES  |     | NULL    |                |
+----------+--------------------------+------+-----+---------+----------------+
```