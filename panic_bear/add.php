<?php
	require_once("service/authentication.php");
	if (!user_authentication())
	{
		logout();
		relocate("login.php");
	}
	if (!gym_climb_authentication())
		relocate("gyms.php");
	
	setcookie("actualPage", "add");
	include("header_in.php");

	if (isset($_COOKIE["gymClimbId"]))
		$gymClimbId = $_COOKIE["gymClimbId"];
	else
		$gymClimbId = "";
	if (isset($_COOKIE["gymClimbName"]))
		$gymClimbName = $_COOKIE["gymClimbName"];
	else
		$gymClimbName = "";

	$conn = mysqli_connect("localhost", "luis", "", "db_climb");
	$q_routes = mysqli_query($conn, "SELECT id, grade, color FROM routes WHERE gymId = '$gymClimbId' AND active = 1 ORDER BY line, id");

	if (isset($_POST["submit"]) && $_POST["submit"] == "ADD CLIMB")
	{
		if (empty($_POST['_date']))
		{
			echo '<script language="javascript">';
			echo "alert('Date is required.')";
			echo '</script>';
		}
		else
			add_to_database();
	}
	function add_to_database()
	{	
		$conn = mysqli_connect("localhost", "luis", "", "db_climb");
		mysqli_query($conn, "INSERT INTO climbs (climbDate, routeId, status, userId) VALUES ('$_POST[_date]', '$_POST[route]', '$_POST[status]', '$_COOKIE[userId]')");
		mysqli_close($conn);
		echo '<script language="javascript">';
		echo "window.location.href = 'history.php';";
		echo '</script>';
	}
?>

<html>
<head>
	<title>Add</title>
	<link rel="stylesheet" type="text/css" href="style/add.css">
</head>

<body>
	<div class="add-container">
		<?php
			echo '<div id="change-div">
				<button id="change-button" onclick="changeFunction()">'.$gymClimbName.'</button>
				</div>';
		?>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="addform" method="post">
			<input type="date" id="add_date" name="_date"></input>
			<select id="add_route" name="route">
				<?php
					while ($row_routes = mysqli_fetch_array($q_routes))
					{
						echo '<option value="'.$row_routes[0].'">'.$row_routes[1].' '.$row_routes[2].'</option>';
					}
					mysqli_close($conn);
				?>
			</select>
			<select id="add_status" name="status">
				<option id="add_top" value="Top">Top</option>
				<option value="Fall">Fall</option>
			</select>
			<input id="add_submit" type="submit" name="submit" value="ADD CLIMB"></input>
		</form>
	</div>
</body>
</html>
	
<script>
	if (window.history.replaceState) 
		window.history.replaceState( null, null, window.location.href );

	function changeFunction()
	{
		window.location.href = 'gyms.php';
	}
</script>

<?php
	include("footer.php");
?>
