<?php 
    define('host', 'localhost');
    define('username', 'root');
    define('password', '');
    define('db','quanlynhanvien');

    function open_database(){
        $conn = mysqli_connect(host, username, password, db);
        if($conn->connect_error){
            die("Error connecting to database");
        }
        return $conn;
    }

    function login($username, $password){
        $conn = open_database();
        $sql = "SELECT * FROM nhanvien WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'User are not exist');
        }
        $hash_password = $data['password'];
        if(!password_verify($password, $hash_password)){
            return array('code' => 2, 'message' => 'Password is not exits'); // password không khớp
        }
        return array('code' => 0, 'message'=>'','data' => $data);
    }
    
    function login_admin($username, $password){
        $conn = open_database();
        $sql = "SELECT * FROM giamdoc WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'User are not exist');
        }
        $hash_password = $data['password'];
        if(!password_verify($password, $hash_password)){
            return array('code' => 2, 'message' => 'Password is not exits'); // password không khớp
        }
        return array('code' => 0, 'message'=>'','data' => $data);
    }

    function get_info_nhanvien($username){
        $conn = open_database();
        $sql = "SELECT * FROM nhanvien WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' =>'No information nhanvien');
        }
        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function get_info_admin($username){
        $conn = open_database();
        $sql = "SELECT * FROM giamdoc WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' =>'No information giamdoc');
        }
        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function change_password($newpwd, $cpwd ,$username){
        if($newpwd != $cpwd){
            return array('code' => 3, 'message' =>'Mật khẩu mới và mật khẩu nhập lại không trùng khớp với nhau');
        }else if($newpwd == "" || $cpwd == ""){
            return array('code' => 3, 'message' =>'Mật khẩu không được để trống');
        }

        $hash = password_hash($newpwd, PASSWORD_DEFAULT);

        $conn = open_database();
        $sql = "UPDATE nhanvien SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss",$hash, $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Thay mật khẩu không thành công');
        }

        return array('code' => 0, 'message' =>'Thay mật khẩu thành công');
    }

    function change_image($image, $username){
        $conn = open_database();
        $sql = "UPDATE nhanvien SET image = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss",$image, $username);
        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }

        return array('code' => 0, 'message' =>'Thay hình đại diện thành công');
    }

    function check_username_exists($username){
        $conn = open_database();
        $sql = "SELECT * from nhanvien where username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return false;
        }
        return true;
    }

    function add_new_nhanvien($name, $username, $pwd, $maPB, $image, $tongngaynghi, $duocnghi, $status){
        if(check_username_exists($username)){
            return array('code' => 2, 'message' => 'Tên tài khoản này đã tồn tại vui lòng nhập tên khác');
        }

        $hash = password_hash($pwd, PASSWORD_DEFAULT);

        $conn = open_database();
        $sql = "INSERT INTO nhanvien (name, username, password, maPB, image, tongngaynghi, duocnghi, status) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiii",$name, $username, $hash, $maPB, $image, $tongngaynghi, $duocnghi, $status);
        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        return array('code' => 0, 'message' =>'Thêm nhân viên mới thành công');
    }

    function get_info_phongban(){
        $conn = open_database();
        $sql = "SELECT * FROM phongban";
        $stmt = $conn->query($sql);
        return array('code' => 0, 'message' =>'', 'data' => $stmt);
    }

    function delete_phongban($maPB){
        $conn = open_database();
        $sql = "DELETE FROM phongban WHERE maPB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$maPB);
        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $stmt->close();
        return array('code' => 0, 'message' =>'Xoá phòng ban thành công');
    }

    function delete_nhan_vien_by_phongban($maPB){
        $conn = open_database();
        $sql = "delete from nhanvien where maPB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
    }

    function add_new_phongban($maPB, $namePB, $truongphong){
        $conn = open_database();
        $sql = "INSERT INTO phongban (maPB, namePB, truongphong) VALUES(?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss",$maPB, $namePB, $truongphong);
        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $stmt->close();
        return array('code' => 0, 'message' =>'Thêm phòng ban mới thành công');
    }

    function get_nhanvien_pb($maPB){
        $conn = open_database();
        $sql = "SELECT * FROM nhanvien WHERE maPB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$maPB);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $result = $stmt->get_result();
        return array('code' => 0, 'message' =>'', 'data' => $result);
    }

    function get_all_info_nhanvien($name)
    {
        $conn = open_database();
        $sql = "SELECT * FROM nhanvien WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$name);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute query');
        }
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'Tìm không thấy thông tin nhân viên');
        }
        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function choose_truong_phong($name, $maPB, $duocnghi){
        $conn = open_database();
        $sql = "update phongban set truongphong = ? where maPB = ?";
        $sql1 = "update nhanvien set duocnghi = ? where name = ?";
        $stmt = $conn->prepare($sql);
        $stmt1 = $conn->prepare($sql1);
        $stmt->bind_param('ss', $name, $maPB);
        $stmt1->bind_param('is', $duocnghi, $name);

        if(!$stmt->execute() || !$stmt1->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Bổ nhiệm tổ trưởng thành công');
    }

    function reject_truong_phong($name, $maPB, $duocnghi, $nameOld){
        $conn = open_database();
        $sql = "update phongban set truongphong = ? where maPB = ?";
        $sql1 = "update nhanvien set duocnghi = ? where name = ?";
        $stmt = $conn->prepare($sql);
        $stmt1 = $conn->prepare($sql1);
        $stmt->bind_param('ss', $name, $maPB);
        $stmt1->bind_param('is', $duocnghi, $nameOld);

        if(!$stmt->execute() || !$stmt1->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Huỷ bổ nhiệm tổ trưởng thành công');
    }

    function get_button_truongphong($maPB){
        $conn = open_database();
        $sql = "select * from phongban where maPB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=> 'cannot execute command');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code'=>2, 'message'=> 'không có dữ liệu');
        }
        $data = $result->fetch_assoc();
        return array('code'=>0, 'message'=> 'cannot execute command','data'=>$data);
    }

    function delete_nhan_vien($id){
        $conn = open_database();
        $sql = "delete from nhanvien where id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Xoá nhân viên thành công');
    }

    function check_truong_phong($name, $maPB){
        $conn = open_database();
        $sql = "select * from nhanvien n, phongban p where n.name = p.truongphong and n.maPB = p.maPB and n.name = ? and n.maPb = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $name, $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return false;
        }
        return true;
    }

    function get_task_by_nhan_vien($name, $maPB){
        $conn = open_database();
        $sql = "select * from task where nhanvien = ? and maPB = ? and status !='Completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $name, $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute message');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code'=>2, 'message'=>'Không có công việc nào được giao');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        return array('code'=>0, 'message'=>'', 'data'=>$data);
    }

    function get_task_completed_by_nhanvien($name, $maPB, $status){
        $conn = open_database();
        $sql = "select * from task where nhanvien = ? and maPB = ? and status = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $name, $maPB, $status);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute message');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code'=>2, 'message'=>'Không có công việc nào đã hoàn thành');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        return array('code'=>0, 'message'=>'', 'data'=>$data);
    }

    function get_task_by_name($nameTask){
        $conn = open_database();
        $sql = "select * from task where tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nameTask);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute message');
        }
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'Tìm không thấy');
        }
        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function get_all_task_by_phong_ban($maPB){
        $conn = open_database();
        $sql = "select * from task where maPB = ? and status !='Completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute message');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'Tìm không thấy');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function get_task_success($maPB){
        $conn = open_database();
        $sql = "select * from task where maPB = ? and status ='Completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $maPB);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute message');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'Tìm không thấy');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function add_new_task($nameTask, $descTask, $nhanvien, $maPB, $dead, $fileNop, $status, $quality){
        $conn = open_database();
        $sql = "insert into task(tenTask, descTask, nhanvien, maPB, deadline, fileTask, status, quality) values (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $nameTask, $descTask, $nhanvien, $maPB, $dead, $fileNop, $status, $quality);
        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Giao task thành công');
    }

    function nop_task($tenTask, $fileNop,$status){
        $conn = open_database();
        $sql = "update task set fileTask = ?, status = ? where tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $fileNop, $status, $tenTask);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Nộp thành công');
    }

    function duyet_task($tenTask, $status, $quality){
        $conn = open_database();
        $sql = "update task set status = ?, quality = ? where tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss',$status, $quality, $tenTask);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Task đã được hoàn thành');
    }

    function reject_task($tenTask, $status){
        $conn = open_database();
        $sql = "update task set status = ? where tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss',$status, $tenTask);

        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Từ chối bài làm yêu cầu làm lại');
    }

    function check_admin($username){
        $conn = open_database();
        $sql = "select * from giamdoc where username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        
        if(!$stmt->execute()){
            return array('code'=> 1, 'message' =>'Cannot execute query');
        }

        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code' => 2, 'message' => 'No exists admin');
        }
        return array('code'=>0, 'message'=>'Là admin');
    }

    function reset_password($username, $pwd){

        $hash = password_hash($pwd, PASSWORD_DEFAULT);

        $conn = open_database();
        $sql = "update nhanvien set password = ? where username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $hash, $username);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }

        return array('code' => 0, 'message' =>'Reset password thành công');
    }

    function delete_task($nameTask){
        $conn = open_database();
        $sql = "DELETE FROM task WHERE tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nameTask);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }

        return array('code' => 0, 'message'=>'Xoá task thành công');
    }

    function edit_task($nameTask, $descTask, $nhanvien, $dead, $id){
        $conn = open_database();
        $sql = "update task set tenTask = ?, descTask = ?, nhanvien = ?, deadline = ? where id =  ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $nameTask, $descTask, $nhanvien, $dead, $id);
        if(!$stmt->execute()){
            return array('code'=>1, 'message'=>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Sửa task thành công');
    }

    function start_task($nameTask, $status){
        $conn = open_database();
        $sql = "update task set status = ? where tenTask = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $status, $nameTask);

        if(!$stmt->execute()){
            return array('code'=> 1, 'message' =>'cannot execute command');
        }
        return array('code' => 0, 'message'=>'Bắt đầu làm task '. $nameTask);
    }

    function xin_nghi($nameNv, $reason, $maPB, $status){
        $conn = open_database();
        $sql = "insert into nghiphep(name, reason, maPB, status) values(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $nameNv, $reason, $maPB, $status);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }

        return array('code' => 0, 'message' =>'Xin nghỉ phép thành công');
    }

    function get_don_nghiphep($nameNv){
        $conn = open_database();
        $sql = "SELECT * from nghiphep where name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nameNv);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code'=>2, 'message'=>'Không có đơn xin nghỉ nào');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        return array('code'=>0, 'message'=>'', 'data'=>$data);
    }

    function get_don_nghiphep_truongphong($maPB){
        $conn = open_database();

        $sql = "SELECT * FROM nhanvien n, nghiphep p WHERE n.name = p.name and n.maPB = p.maPB and p.maPB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $maPB);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            return array('code'=>2, 'message'=>'Không có đơn xin nghỉ nào');
        }
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        return array('code'=>0, 'message'=>'', 'data'=>$data);
    }

    function approve_xin_nghi_by_truong_phong($nameNV, $status, $id){
        $conn = open_database();

        $sql = "update nghiphep set status = ? where name = ? and id = ?";
        $sql1 = "update nhanvien set tongngaynghi = (tongngaynghi + 1) where name = ?";
        $stmt = $conn->prepare($sql);
        $stmt1 = $conn->prepare($sql1);
        $stmt->bind_param('ssi', $status, $nameNV, $id);
        $stmt1->bind_param('s', $nameNV);

        if(!$stmt->execute() || !$stmt1->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Duyệt nghỉ phép thành công');
    }

    function reject_xin_nghi_by_truong_phong($nameNV, $status, $id){
        $conn = open_database();

        $sql = "update nghiphep set status = ? where name = ? and id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $status, $nameNV, $id);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'cannot execute command');
        }
        return array('code'=>0, 'message'=>'Từ chối nghỉ phép thành công');
    }

    function get_all_nghiphep_admin(){
        $conn = open_database();
        $sql = "SELECT * FROM phongban p, nghiphep n WHERE p.truongphong = n.name";
        $stmt = $conn->query($sql);
        
        if($stmt->num_rows == 0){
            return array('code' => 1, 'message' =>'Không có đơn xin nghỉ phép');
        }
        $data = array();
        while($row1 = $stmt->fetch_assoc()){
            $data[] = $row1;
        }
        return array('code' => 0, 'message' =>'', 'data' => $data);
    }

    function update_status_staff($name){
        $conn = open_database();
        $sql = "UPDATE nhanvien SET status = status + 1 WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $name);

        if(!$stmt->execute()){
            return array('code' => 1, 'message' =>'Cannot execute change status of staff');
        }
        return array('code'=>0, 'message'=>'Change success');
    }
?>