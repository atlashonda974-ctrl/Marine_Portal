<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Create connection to Oracle
require_once 'config.php';
if (!$conn) {
   $e = oci_error();
   trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
   exit;
}
else {
 //echo "Connected to Oracle";
 //$client_code = $_GET['client_code'];
			
				
						$query ="SELECT PVC_CODE, PVC_DESC, PVC_TYPE FROM ailmis.uw_vessel Where PVC_TYPE = 'D'";
						
						$combo = oci_parse($conn, $query);
					
												//Current
                                                if (!$combo) {
                                                                $e1 = oci_error($conn);
                                                                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                                                }
                                                $r1 = oci_execute($combo);
                                                if (!$r1) {
                                                                $e1 = oci_error($combo);
                                                                trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
                                                }
												
												
												$count = 0;
												$posts_arr = array();
                                                  while($res = oci_fetch_array($combo, OCI_ASSOC+OCI_RETURN_NULLS)) {

														
														 $post_item = array(
																'STATUS' => "Exist",	
																'PVC_CODE' => ($res['PVC_CODE'] !== null ? htmlentities($res['PVC_CODE'], ENT_QUOTES) : "&nbsp;"),
																'PVC_DESC' => ($res['PVC_DESC'] !== null ? htmlentities($res['PVC_DESC'], ENT_QUOTES) : "&nbsp;"),
																'PVC_TYPE' => ($res['PVC_TYPE'] !== null ? htmlentities($res['PVC_TYPE'], ENT_QUOTES) : "&nbsp;")
																
															  );	
														
														array_push($posts_arr, $post_item);
                                                }
												
												
												
											

												echo json_encode($posts_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
												
												
												
}
// Close the Oracle connection
oci_close($conn);
?>
