<?php 

	include 'FDBL.php';
	$fdbl = new FDBL();

	// Login Credentials
	$type = $_POST['type'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$isAvailAdmin = $fdbl->select('users','*',['name' => 'admin001']);
	$isLogin = $fdbl->count($isAvailAdmin);
	if($isLogin == 0)
	{
		$status = $fdbl->insert('users', [
							'name' => 'Admin001',
						    'email' => $email,
						    'password' => $password,
				   		]);
		if($status)
		{	
			$fdbl->setSessionMsg('Admin Created Successfully! Login to Access your panel');
			header('location:../login.php');
		}

	}
	else
	{
		switch ($type) {
			case 'admin':
				$table = 'users';
				
				break;
			
			case 'doctor':
				$table = 'doctors';
				
				break;
			
			case 'user':
				$table = 'users';
				
				break;

		}

		$table = ($type == 'admin') ? 'users' : $type;
		$loginData = $fdbl->select($table, '*',[
						'email' => $email,
						'AND',
						'password' => $password,
					]);

		$isLogin = $fdbl->count($loginData);

		
		if($isLogin > 0)
		{
			$loginData = $fdbl->fetchArray($loginData);
			$userName = ($loginData['name'] == 'Admin001' &&  $type == 'admin') ? 'Admin' : $loginData['name'];
			
			$flag = false;
			if($type == 'users' && $userName != 'Admin001')
			{
				$flag = true;
				$fdbl->setSessionMsg('user','isUser');	
				$fdbl->setSessionMsg($loginData['address'] . "<br> Age:" . $loginData['age'],'subText');	
				$fdbl->setSessionMsg('Connect your doctor from your home.','wlcmMsg');	
			}	
			else if($type == 'doctors')
			{
				$flag = true;
				$fdbl->setSessionMsg('doctor','isUser');
				$userName = 'Dr. ' . $userName;
				$fdbl->setSessionMsg($loginData['qualification'],'subText');	
				$fdbl->setSessionMsg('Connect your patient from your home.','wlcmMsg');
			}
			else if($type == 'admin' && $loginData['name'] == 'Admin001')
			{
				$flag = true;
				$fdbl->setSessionMsg('admin','isUser');
				$fdbl->setSessionMsg($loginData['email'],'subText');	
				$fdbl->setSessionMsg(':)','wlcmMsg');
			}

			if($flag)
			{
				$fdbl->setSessionMsg($loginData['id'], 'userId');
				$fdbl->setSessionMsg($userName, 'userName');
				header('location:../home.php');
			}
			else
			{
				$fdbl->setSessionMsg('Incorrect Login Credentials');
				header('location:../login.php');
			}
		}
		else
		{
			$fdbl->setSessionMsg('Incorrect Login Credentials');
			header('location:../login.php');
			
		}
			
	}

 ?>