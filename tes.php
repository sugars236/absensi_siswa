<?php

$conn= new mysqli ("localhost","root","","todo_list");

if ($conn->connect_error){
    die("Connection Failed: ". $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['add_task'])){
    $task_name  = $_POST['name'];
    $due_date   = $_POST['due_date'];
    $prioritas  = $_POST['prioritas'];
    $status     = 'pending';

    $stmt = $conn->prepare("INSERT INTO tasks (name,due_date,prioritas,status) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$task_name,$due_date,$prioritas,$status);
    $stmt->execute();
    $stmt->close();
    header("Location: ". $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['update_task'])){
    $task_id    = $_POST['id'];
    $task_name  = $_POST['name'];
    $due_date   = $_POST['due_date'];
    $prioritas  = $_POST['prioritas'];
    $status     = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tasks SET name=?,due_date=?,prioritas=?,status=? where id=?");
    $stmt->bind_param("ssssi",$task_name,$due_date,$prioritas,$status,$task_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ". $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['delete_task'])){
    $task_id = $_POST['id'];
    $conn->query("DELETE FROM tasks WHERE id = $task_id");
    header("Location: ". $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query("SELECT * FROM tasks ORDER BY due_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO LIST</title>
    <script>
        function confirmEdit {
            return confirm("Apakah anda ingin mengedit data tersebut?"); 
        }
        function confirmDelete {
            return confirm("Apakaha anda ingin menghapus data tersebut");
        }

    </script>
</head>
<body>
    <div class=Container>
        <h1>TODO LIST</h1>
    <form method="POST">
        <div class="form-row">
            <input type="text" name="name" placeholder="Enter a New Task" required>
            <input type="datetime-local" name="due_date" required>
            <select name="prioritas" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <button type="submit" name="add_task">Simpan</button>
        </div>
    </form>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>TASKS</th>
                <th>DUE DATE</th>
                <th>PRIORITAS</th>
                <th>STATUS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no=1;
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $no++?></td>
                <td><?php echo $row['name'];?></td>
                <td><?php echo $row['due_date'];?></td>
                <td><?php echo $row['prioritas'];?></td>
                <td><?php echo $row['status'];?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $row['id'];?>"required>
                        <input type="text" name="name" value="<?php echo $row['name'];?>"required>
                        <input type="datetime-local" name="due_date" value="<?php echo date ('Y-m-d\TH:i' , strtotime ($row['due_date']));?>"required>
                        <select name="prioritas" required>
                            <option value="Low" <?php echo $row['prioritas'] === 'Low' ? 'selected':'';?>>LOW</option>
                            <option value="Medium" <?php echo $row['prioritas'] === 'Medium' ? 'selected':'';?>>Medium</option>
                            <option value="High" <?php echo $row['prioritas'] === 'High' ? 'selected':'';?>>High</option>
                        </select>
                        <select name="status" required>
                            <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected':'';?>>Pending</option>
                            <option value="completed" <?php echo $row['status'] === 'completed' ? 'selected':'';?>>Completed</option>
                        </select>
                        <button type="submit" name="update_task">Edit</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $row['id'];?>"required>
                        <button type="submit" name="delete_task">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile;?>
        </tbody>
    </table>
    </div>
</body>
</html>