<?php


class userController{

    private $userManager;
    private $user;

    public function __construct($db1){

        include('./Model/UserManager.class.php');
        $this->userManager = new UserManager($db1);

        $this->db = $db1;
    }

    public function login(){
        $page = 'login';
        require('./View/main.php');
    }

    public function create()
    {
        $page = 'create';
        require('./View/main.php');
    }

    public function unauthorized(){
        $page = 'unauthorized';
        require('./View/main.php');
    }

    public function listUser(){
        if(isset($_SESSION['user']) && $_SESSION['user']->getAdmin() == "1"){

            $page= 'listUser';
            $users= $this->userManager->findAll();
            require('./View/main.php');

        }else{
			$this->unauthorized();
        }
    }

    public function editUser(){
        if(isset($_GET['userId']) && isset($_SESSION['user']) && $_SESSION['user']->getAdmin() == "1"){
            $page = 'editUser';
            $result = $this->userManager->findOne($_GET["userId"]);
            require ('./View/main.php');
			
        }
        else{
            $this->unauthorized();
        }

    }
	
    public function deleteUser(){
        if(isset($_GET['userId']) && isset($_SESSION['user']) && $_SESSION['user']->getAdmin() == "1"){
            $page = 'deleteUser';
            $result = $this->userManager->findOne($_GET["userId"]);

            require ('./View/main.php');
        }else{
            $this->unauthorized();
        }
    }


    public function doLogin(){

        $this->user = new User();


        if (isset($_POST['email']) && isset($_POST['password'])) {
            $this->user->setEmail($_POST['email']);

            $this->user->setPassword($_POST['password']);
            $result = $this->userManager->login($this->user);

            if ($result) :
                $info = array("Connexion reussie", "success");
            else :
                $info = array("Identifiants incorrects.", "danger");
            endif;
        }

        require('./View/main.php');

    }

    public function doLogout(){
        if (isset($_SESSION['user'])) {
            $this->userManager->logout();
        }

        require('./View/main.php');
    }

    public function doCreate(){
        var_dump($_POST);
        if (isset($_POST) && isset($_POST["password"])
            && isset($_POST["email"]) && isset($_POST["firstname"]) && isset($_POST["lastname"])
            && isset($_POST["address"]) && isset($_POST["school"]) && isset($_POST["city"])
           ) {


            $user["password"] = $_POST["password"];
            $user["email"] = $_POST["email"];
            $user["firstName"] = $_POST["firstname"];
            $user["lastName"] = $_POST["lastname"];
            $user["address"] = $_POST["address"];
            $user["school"] = $_POST["school"];
            $user["city"] = $_POST["city"];
            if (isset($_POST["admin"])) $user["admin"] = intval($_POST["admin"]);

            $this->user = new User($user);

            $this->userManager->create($this->user);

            header("Location: index.php?ctrl=user&action=login");
        }
    }

    public function doEditUser(){
		echo"up";
        try{
                $user = $this->userManager->findOne($_GET["userId"]);


                $this->userManager->update($user);
                $this->userManager->updateRole($user);

            header("Location: index.php?ctrl=user&action=listUser");
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function doDeleteUser(){
        try{
            $this->user = $this->userManager->findOne($_POST["user"]);
            $this->userManager->delete($this->user);

            header("Location: index.php?ctrl=user&action=listUser");
        } catch (\Exception $e){
            echo $e->getMessage();
        }
    }


}