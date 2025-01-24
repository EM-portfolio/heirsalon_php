<?php
include 'functions.php';
$csrfToken = getToken();

  $conn = getDbConnection();
  if (isset($_GET['query'])) {
    $query = htmlspecialchars(strval($_GET['query']));
    $query = mb_convert_kana($query, "s");
    $keywords = explode(" ", $query);
    $count = 0;
    foreach ($keywords as $keyword) {
      if($count >= 1){
        $sql .= " OR user_table.mailaddress LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.name LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.phone LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.birthdate LIKE '%" . $keyword . "%'";
      }else{
        $sql .= "user_table.mailaddress LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.name LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.phone LIKE '%" . $keyword . "%'";
        $sql .= " OR user_card_table.birthdate LIKE '%" . $keyword . "%'";
      }
      ++$count;
      }
    $basesql = "
      SELECT
      user_table.user_id AS user_id,
      user_table.mailaddress AS mailaddress,
      user_card_table.name AS name,
      user_card_table.phone AS phone,
      user_card_table.birthdate
      FROM user_table
      INNER JOIN
      user_card_table ON user_table.user_id = user_card_table.user_id
      WHERE
    ";
    $fullsql = $basesql . $sql;
    $stmt = $conn -> prepare($fullsql);
    $row_cnt = $result->num_rows;
    if($stmt->execute()){
      $search_result = $stmt->get_result();
    }else{
      $search_result = "エラーが発生しました:" . $stmt->error;
    }
    if($row_cnt > 1){
      if(!is_string($search_result)){
        echo "<table>";
        echo '<tr class="thead">';
        echo "<th>氏名</th>";
        echo "<th>メールアドレス</th>";
        echo "<th>電話番号</th>";
        echo "</tr>";
        while($result = $search_result -> fetch_assoc()){
          echo '<tr class="tbody" data-user-id="' . $result['user_id'] . '">';
          echo '<td>' . htmlspecialchars($result['name']) . "</td>";
          echo '<td>' . htmlspecialchars($result['mailaddress']) . "</td>";
          echo '<td>' . htmlspecialchars($result['phone']) . "</td>";
          echo "</tr>";
        }
        echo "</table>";
      }else{
        echo $search_result;
      }

    }else{
      echo "<p>検索結果は0件でした。";
    }

  }// if_end




?>