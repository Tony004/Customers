<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<title>Гостевая книга</title>
		<link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="css/styles.css">
	</head>

<?php
//Данные для подключения к БД
    $host = 'localhost';
    $user = 'root';
    $password = 'root';
    $db_name = 'test';
//Флаг для видимости блока
    $flag = 'hidden';
//Подключение к БД
    $link = mysqli_connect($host, $user, $password, $db_name);
//Запрос для разрешения русских слов
    mysqli_query($link, "SET NAMES 'utf8'");
//Добавление записи
    if(!empty($_POST)){
        $name = $_POST["name"];
        $text = $_POST["text"];

        $query = "INSERT INTO customers set name='$name', currentDate=now(), message='$text'";
        if (mysqli_query($link, $query) === TRUE){
            $flag = 'visible';
        }
    }

//pagination
	$query = "SELECT COUNT(*) as count FROM customers";
	$result = mysqli_query($link, $query);
	$count = mysqli_fetch_assoc($result)["count"];

	if (isset($_GET["page"])){
			$page = $_GET["page"];
	}
	else{
		$page = 1;
	}
	$notes = 3;
	$from = ($page-1) * $notes;
	$pagesCount = ceil($count / $notes);
	$prev = $page-1;
	$next = $page+1;

//Получение данных из БД
    $query = "SELECT * FROM customers GROUP BY id DESC LIMIT $from, $notes";
    $result = mysqli_query($link, $query);
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    $data = array_reverse($data);


?>



<body>
    <div id="wrapper">
        <h1>Гостевая книга</h1>
<!--Pagination-->
		<div>
			<nav>
			  <ul class="pagination">
				<li class="disable">
					<?php
					if ($page > 1)
						echo "<a href=\"?page=$prev\"  aria-label=\"Previous\"><span>&laquo;</span></a>";
					?>
				</li>
				<?php
					for ($i = 1; $i <= $pagesCount; $i++){
						if ($page == $i)
							$class = " class='active'";
						else
							$class = '';

							echo "<li$class><a href=\"?page=$i\">$i</a></li>";
					}
				?>
				<li>
					<?php
					if ($page < $pagesCount)
						echo "<a href=\"?page=$prev\"  aria-label=\"Previous\"><span>&raquo;</span></a>";
					?>
				</li>
			  </ul>
			</nav>
		</div>

        <?php
        //Вывод на экран
            foreach($data as $elem){
        ?>
                <div class="note">
                    <p>
                        <span class="date"><?php echo $elem["currentDate"]; ?></span>
                        <span class="name"><?php echo $elem["name"]; ?></span>
                    </p>
                    <p>
                        <?php echo $elem["message"]; ?>
                    </p>
                </div>
        <?php
                }
        ?>

        <div class="info alert alert-info" style="visibility: <?php echo $flag; ?>">
            Запись успешно сохранена!
        </div>

        <div id="info alert alert-info">
            <form action="" method="POST">
                <p><input class="form-control" placeholder="Ваше имя" name="name"></p>
                <p><textarea class="form-control" placeholder="Ваш отзыв" name="text"></textarea></p>
                <p><input type="submit" class="btn btn-info btn-block" value="Сохранить"></p>
            </form>
        </div>
    </div>
</body>
</html>
