<?php
  /**
   * 接続したDBにmembersテーブルがあるかを調べ、なかった場合は作成し、テスト用団員データを挿入する
   * 注意：DBに接続した後で使用してください。
   */
  //membersテーブルがあるかどうかを調べる
  $q_exist = "SHOW TABLES LIKE 'members'";
  $res_show = $mysqli->query($q_exist);
  if($res_show==NULL){
    //存在しなかった場合、指定のフォーマットでテーブルを作成
    $q_create = 
    "CREATE TABLE `members` (
      `id` int(5) UNSIGNED ZEROFILL NOT NULL,
      `email` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
      `password` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
      `last_name` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
      `first_name` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
      `kana` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
      `grade` int(2) DEFAULT NULL,
      `part` varchar(1) CHARACTER SET utf8mb4 DEFAULT NULL,
      `token` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
      `validation_time` datetime DEFAULT NULL,
      `login_failure` int(2) NOT NULL DEFAULT '0',
      `admin` int(1) DEFAULT NULL,
      `status` int(1) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q_key = 
    "ALTER TABLE `members` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`)";
    $q_constraint = 
    "ALTER TABLE `members`
    MODIFY `id` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";

    $isComplete = $mysqli->query($q_create) && $mysqli->query($q_key) && $mysqli->query($q_constraint);
    if(!$isComplete){
      echo("Query error: cannot create members table -- ".$mysqli->error);
      exit();
    }
    //テスト用団員データを挿入
    $q_insert = 
    "INSERT INTO `members` (`id`, `email`, `password`, `last_name`, `first_name`, `kana`, `grade`, `part`, `token`, `validation_time`, `login_failure`, `admin`, `status`) VALUES
    (00001, '1111@mail', '\$2y\$10\$8xDzzqXZBjDkZnm2.BA8..cRn64HMBXHtY5YbdszXzuaP2cqZ2nAS', '渉外', 'チーフ子', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
    (00002, '2222@mail', '\$2y\$10\$v.OKPm9dQtFsfof9GqbpquHE9h7ufFrkE5RGhpKWPP6DAYP1e5RtO', '渉外', '花子', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
    (00003, '3333@mail', '\$2y\$10\$CXo99ORVAESp8.8JuQe13OtsvShcfGIK1IBVCYQ4NRzd3Td7q52Um', '団員', '次郎', NULL, 18, 'T', NULL, NULL, 0, NULL, 0),
    (00004, '4444@mail', '\$2y\$10\$nLBjzQVXGOjBcxCHG4hKmu6seGxYtOlwQfAzO4e/j7z46tF7zd3GS', '団員', '太郎', NULL, 18, 'B', NULL, NULL, 0, NULL, 0),
    (00005, '14b@mail', '\$2y\$10\$s2X2xjlcLvbLhCvYeMpf.efOzRv4TQp2r7AdNF2x5b.l9rBkGEp8G', '安桜', 'ブンブン', NULL, 14, 'B', NULL, NULL, 0, NULL, 0),
    (00006, '16web@mail', '\$2y\$10\$niR34xTXFh5g6rLiQ/pf.ue5qbBqMr1BUFczPtr3/xXguXk8jo4nS', 'ウェブ管', 'ボス太郎', NULL, 16, 'B', NULL, NULL, 0, 1, 0),
    (00007, '16t@mail', '\$2y\$10\$Ba72.o9o4YPy.I.m67H2NeandS/j1dlNBnt7m9QrjEcZ8rAK/pSz.', 'マイン', 'クラフト', NULL, 16, 'T', NULL, NULL, 0, NULL, 0),
    (00008, '17s@mail', '\$2y\$10\$BSNv5j59aDPMK0KAYqaiZuXsO8FzRPYP/KoKbffaEWdeC7dowruze', '元渉外', 'チーフ子', NULL, 17, 'S', NULL, NULL, 0, NULL, 0),
    (00009, 'front@mail', '\$2y\$10\$.HTbAzR20.s4JEFtl7I04eUuBLY0ZJiyHe.tgu.OFufB8vmSLLdmi', 'フロント', 'チーフ子', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
    (00010, '16front@mail', '\$2y\$10\$71D/RuHD9AgmuTc2IfUI2eoUfpkG5l7VxdigDmsMDP42hE7JSSTY.', 'フロント', '雑用', NULL, 16, 'B', NULL, NULL, 0, 1, 0),
    (00011, '18web@mail', '\$2y\$10\$mhM3eLnVPg8ZmYda1TWXI.6GEu5JuhjprxROWsb9YNTZVX4Ks6jVu', 'ウェブ管', 'エース', NULL, 18, 'T', NULL, NULL, 0, 1, 0),
    (00012, 'stmn@mail', '\$2y\$10\$wjwt/UgvFyMZMC8n/eYGC.lgmccaSTb7CKBFEWh3/b7x2c//52IKK', 'ステマネ', '花子', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
    (00013, 'sfukui@mail', '\$2y\$10\$tcxNjfSqW.UWhxKR3RyDveCIHi0AXkDTQ424pnR4lOqSR1tsqrvWC', 'ソプラノ', 'ふくい', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
    (00014, 'afukui@mail', '\$2y\$10\$jphydTNDzHt.5TuCDMsMAuKM4PsF7wTkiOrjggzRpu5zbfD3rHrBq', 'アルト', 'ふくい', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
    (00015, 'tfukui@mail', '\$2y\$10\$5BGOUzZGME5DofpEXZMt/OA03Sscwld8G1UnQm4UZVtNUGJfopnO2', 'テナー', 'ふくい', NULL, 18, 'T', NULL, NULL, 0, NULL, 0),
    (00016, 'bfukui@mail', '\$2y\$10\$lq.7f9OLWYnsZWBKPbRH..P0CiTG3exvsFdjG//Cx73hR36ZhHXfy', 'ベース', 'ふくい', NULL, 18, 'B', NULL, NULL, 0, NULL, 0),
    (00017, 'sshogai@mail', '\$2y\$10\$Ikdi0OXPGm66CphE99W.g.k1h030Go4rN13jNbOCSbSk/fSkmZx5S', '渉外', 'ソプ子', NULL, 19, 'S', NULL, NULL, 0, NULL, 0),
    (00018, 'ashogai@mail', '\$2y\$10\$A6JXnn1tHHmV1joOvoSg4uhYYEjr7pM0J11bxIILrI5IIFyBPxGpy', '渉外', 'アル子', NULL, 19, 'A', NULL, NULL, 0, NULL, 0),
    (00019, 'tshogai@mail', '\$2y\$10\$mHNkeHVHHuiNiCOGEH33iuyNwo5NxUhrTCY95sBWKUaS2GgQ4QVIi', '渉外', 'テナ太郎', NULL, 19, 'T', NULL, NULL, 0, NULL, 0),
    (00020, 'bshogai@mail', '\$2y\$10\$tpM03fD4cTUwskoiB9CRYuei25DADsO/q0uYZOvg9qD4qpSzZfpx2', '渉外', 'バス太郎', NULL, 19, 'B', NULL, NULL, 0, NULL, 0),
    (00021, '20s@mail', '\$2y\$10\$4mhiyJuB8bdTCYHGagbcxOD0Gwh8BOzm/wXHY7iS4hceB1F5j9.BS', '新入', 'えすこ', NULL, 20, 'S', NULL, NULL, 0, NULL, 0),
    (00022, '20a@mail', '\$2y\$10\$UDp7kdxEjv3o28ZiZkIFjOpgz7tOUvwHB1kfqlXZeh3wwqqFdK2ki', '新入', 'えいこ', NULL, 20, 'A', NULL, NULL, 0, NULL, 0),
    (00023, '20t@mail', '\$2y\$10\$J4H5agTnDGoE9d0mv6j1peYpbvASlKhGEIGJsgMJaefZxkGwFijZ6', '新入', 'ていた', NULL, 20, 'T', NULL, NULL, 0, NULL, 0),
    (00024, '20b@mail', '\$2y\$10\$PG9XCjDGdsQVA1qU8EXk2ud6yzGxansfLzz4P0KVNUtKG.OxrpnjK', '新入', 'びいた', NULL, 20, 'B', NULL, NULL, 0, NULL, 0),
    (00025, '20front@mail', '\$2y\$10\$wU9fsxza6CSav45zZgS8W.dYMKcGerR.cQr/b739n.2Eu2QnSsLe6', 'フロント', 'サブ子', NULL, 20, 'S', NULL, NULL, 0, NULL, 0)";
    $isComplete = $mysqli->query($q_insert);
    if(!$isComplete){
      echo("Query error: cannot insert to members table -- ".$mysqli->error);
      exit();
    }
  }
  
  if($res_show!=NULL){
    $res_show->close();
  }