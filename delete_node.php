<?php
	$node=$_GET['nodeID'];
	$sessionID = $_GET['sessionID'];
	$timestep = $_GET['timestep'];
	$timestep2 = $timestep+1;
	#	echo $node;
	$link = mysqli_connect('jsalvitdbinstance.cku3opv9prdt.us-east-1.rds.amazonaws.com','trust_user','trust123', 'trust');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	if(substr($_GET['nodeID'],0,4) == 'fact'){
		  //Do something
#		  echo "id is ".substr($_GET['nodeID'],4);
			$sql="call copy_trust('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

			$sql="call copy_beliefs('".$sessionID."',".$timestep.",".$timestep2.",".substr($_GET['nodeID'],4).",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

/*
			$sql="call copy_fact('".$sessionID."',".$timestep.",".$timestep2.",".substr($_GET['nodeID'],4).");";
//			$sql="call copy_fact('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
//			print_r($result);
			mysqli_free_result($result);

			$sql="call copy_rules('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);
*/
			$sql="call copy_question('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);
			mysqli_close($link);
			
			header( 'Location: index.php?sessionID='.$sessionID.'&timestep='.$timestep2 );

 	}else if(substr($_GET['nodeID'],0,5) == 'agent'){
 #			echo "" .substr($_GET['nodeID'],5);
			$sql="call copy_trust('".$sessionID."',".$timestep.",".$timestep2.",".substr($_GET['nodeID'],5).");";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

			$sql="call copy_beliefs('".$sessionID."',".$timestep.",".$timestep2.",-1,".substr($_GET['nodeID'],5).");";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

			$sql="call copy_question('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);
			mysqli_close($link);
			
			header( 'Location: index.php?sessionID='.$sessionID.'&timestep='.$timestep2 );
 	}else if(substr($_GET['nodeID'],0,4) == 'rule'){
 #			echo "" .substr($_GET['nodeID'],5);
			$sql="call copy_trust('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

			$sql="call copy_beliefs('".$sessionID."',".$timestep.",".$timestep2.",".substr($_GET['nodeID'],4).",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);

			$sql="call copy_question('".$sessionID."',".$timestep.",".$timestep2.",-1);";
 			$result=mysqli_query($link,$sql);
			mysqli_free_result($result);
			mysqli_close($link);
			
			header( 'Location: index.php?sessionID='.$sessionID.'&timestep='.$timestep2 );
 	}else{
			mysqli_close($link);
			
			header( 'Location: index.php?sessionID='.$sessionID.'&timestep='.$timestep );
		
		
 	}

?>