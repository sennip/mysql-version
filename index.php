<?php
	include 'header.php';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>ArgTrust Decision Making System</title>
<meta charset="utf-8">

<link href="./jQuery-contextMenu-master/src/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
<link href="./jQuery-contextMenu-master/prettify/prettify.sunburst.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css">

<script src="./jQuery-contextMenu-master/jquery-1.8.2.min.js" type="text/javascript"></script>
<script src="./jQuery-contextMenu-master/src/jquery.ui.position.js" type="text/javascript"></script>
<script src="./jQuery-contextMenu-master/src/jquery.contextMenu.js" type="text/javascript"></script>
<script src="./jQuery-contextMenu-master/prettify/prettify.js" type="text/javascript"></script>
<script src="./jQuery-contextMenu-master/screen.js" type="text/javascript"></script>

<script type="text/javascript" src="./prototype/prototype.js"></script>
<script type="text/javascript" src="./path/path.js"></script>
<script type="text/javascript" src="canviz.js"></script>
<script type="text/javascript" src="scripts_common.js"></script>

<script type="text/javascript">
		var mySessionID = "<?php echo $sessionID;?>";
		var myTimestep = "<?php echo $timestep;?>";
		var myCanviz;
		var graphScale = .5;
		document.observe('dom:loaded', function() {
			myCanviz = new Canviz('canviz');
			myCanviz.setScale(graphScale);
			if(mySessionID.length > 0){
				myCanviz.load("graphs2/<?php echo $sessionID; ?>.gv");
				//alert("canviz height= "+ document.getElementById('canviz_canvas_1').height + " window height =" + window.innerHeight);

			}
		}); 
			
		function updategraph(graphType, graphID){
			url = "index.php?sessionID=<?php echo $sessionID; ?>&timestep=<?php echo $timestep; ?>";
			if(graphType == 'agent')
				window.location.href=url+"&agentID="+graphID;
			else if(graphType == 'belief')
				window.location.href=url+"&beliefID="+graphID;
			else if(graphType == 'rule')
				window.location.href=url+"&ruleID="+graphID;
			else if(graphType == 'argument')
				window.location.href=url+"&argumentID="+graphID;
			else if(graphType == 'conclusion')
				window.location.href=url+"&conclusionID="+graphID;
			else
				window.location.href=url;
			
		}

		function load_graph () {
			myCanviz.load(load_url());
		};

		function load_url () {
			return mygraph;
			//return './graphs/' + mygraph;
		};

		function change_scale(inc) {
			graphScale+=inc;
			myCanviz.setScale(graphScale);
			myCanviz.draw()
		};

		function resize() {
				//alert(document.getElementById('canviz_canvas_1').height/window.innerHeight);
				change_scale((1-document.getElementById('canviz_canvas_1').height/window.innerHeight)/2);

		}

</script>

<script type="text/javascript">
		function display_alert(node_id) {
		/*	var id = node_id;
			var left = (screen.width/2)-(200/2);
  			var top = (screen.height/2)-(200/2);
			newwindow=window.open('onclick.php?id='+id,'delete node','height=200,width=200,left='+left+' ,top='+top+',resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no, status=yes')
				if (window.focus) {newwindow.focus()}
				return false;		
			
			alert(node_id);
			$(this).attr('id', 'node_id');*/
		}

</script>

<script type="text/javascript">
			jQuery.noConflict();
			
			jQuery(window).load(function () {
			     width= jQuery('#tabContainer').outerWidth();
			     jQuery(".zoom").css("right", width);
			     jQuery(".zoom").css("display", "block");
			});
			
			function get_id(label, name) {
				jQuery("a[title='"+label+"']").attr('id', name);		
			};

			jQuery(function(){
			    jQuery.contextMenu({
			        selector: '#canviz', 
			        trigger:'left',
			        callback: function(key, options) {
			            var m = "clicked: " + key;
			            var id = jQuery(this).attr('id');
			            window.console && console.log(m) || alert(id); 
			        },
			        items: {
			        "fold1a": {
			                "name": "New Node", 
			                icon: "add",
			                "items": {
			                    "fold2": {
			                        "name": "Agent", 
			                        "items": {
			                            addAgentLabel: {
							                name: "From Agent Name", 
							                type: 'text', 
							                value: "Ex: Jon"
							            },
			                            addAgentLabel2: {
							                name: "To Agent Name", 
							                type: 'text', 
							                value: "Ex: Mary"
							            },
							            addAgentTrust: {
							                name: "Trust Level", 
							                type: 'text', 
							                value: "Ex: .75"
							            },
							            sep4: "---------",
							            addagent: {
							                name: "Add Agent", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
								                var label = document.getElementsByName("context-menu-input-addAgentLabel")[0].value.toString();
												var toTrust = document.getElementsByName("context-menu-input-addAgentLabel2")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addAgentTrust")[0].value.toString();
								                window.location.assign("add_node.php?type=agent&fromAgent=" + label + "&toAgent=" + toTrust + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}
		            					}
				                    }
			                    },
			                    "fold3": {
			                        "name": "Fact", 
			                        "items": {
			                        	/*addFactAgent: {
									        name: "Agent", 
									        type: 'select', 
									        options: {me: 'Me', uav: 'UAV', recon: 'Recon', informant:'Informant'}, 
								            selected: me
							        	},*/
							        	addFactAgent: {
									        name: "Agent", 
							                type: 'text', 
							                value: "Ex: UAV"
								        },
			                            addFactLabel: {
							                name: "Fact", 
							                type: 'text', 
							                value: "Ex: Increased(Gunfire)"
							            },
							            addFactTrust: {
							                name: "Belief Level", 
							                type: 'text', 
							                value: ".5"
						            	},
							            sep4: "---------",
							            addfact: {
							                name: "Add Fact", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
							                	/*var e = document.getElementsByName("context-menu-input-addFactAgent");
												var agent = e[0].options[e[0].selectedIndex].text;
							                	alert(agent);*/
							                	var agent = document.getElementsByName("context-menu-input-addFactAgent")[0].value.toString();
								                var label = document.getElementsByName("context-menu-input-addFactLabel")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addFactTrust")[0].value.toString();
								                window.location.assign("add_node.php?type=fact&agent=" + agent + "&belief=" + label + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}

						            	}
		            					
				                    }
			                    },
			                    "fold4": {
			                        "name": "Rule", 
			                        "items": {
			                        	/*addRuleAgent: {
									        name: "Agent", 
									        type: 'select', 
									        options: {1: 'Me', 2: 'UAV', 3: 'Recon', 4:'Informant'}, 
								            selected: 1
							        	},*/
							        	addRuleAgent: {
									        name: "Agent", 
							                type: 'text', 
							                value: "Ex: UAV"
								        },
			                            addRulePremise: {
							                name: "Premise", 
							                type: 'text', 
							                value: "Ex: Increased(Gunfire)"
							            },
							            addRuleConclusion: {
							                name: "Conclusion", 
							                type: 'text', 
							                value: "Ex: NOT Safe(Mission)"
							            },
							            addRuleTrust: {
							                name: "Belief Level", 
							                type: 'text', 
							                value: ".5"
						            	},
							            sep4: "---------",
							            addrule: {
							                name: "Add Rule", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
							                	var agent = document.getElementsByName("context-menu-input-addRuleAgent")[0].value.toString();
								                var premise = document.getElementsByName("context-menu-input-addRulePremise")[0].value.toString();
												var conclusion = document.getElementsByName("context-menu-input-addRuleConclusion")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addRuleTrust")[0].value.toString();
								                window.location.assign("add_node.php?type=rule&agent=" + agent + "&premise=" + premise + "&conclusion=" + conclusion + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
		            						}
		            					}
				                    }
			                    },
			                    "fold5": {
			                        "name": "Question", 
			                        "items": {
			                            addQuestionAgent: {
							                name: "Agent", 
							                type: 'text', 
							                value: "Ex: Recon"
							            },
							            addQuestion: {
							                name: "Question", 
							                type: 'text', 
							                value: "Ex: InArea(HVT)"
							            },
							            sep4: "---------",
							            addQuestionButton: {
							                name: "Add Question", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
								                var agent = document.getElementsByName("context-menu-input-addQuestionAgent")[0].value.toString();
												var question = document.getElementsByName("context-menu-input-addQuestion")[0].value.toString();
								                window.location.assign("add_node.php?type=question&agent=" + agent + "&question=" + question+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}
		            					}
				                    }
			                    }
			                }
			            },
			            "sep": "---------",
			            "cancel": {
			                name: "Cancel", 
			                icon: "quit",
			                callback: function(key, options) {

						    }
			        }
			    }
			});
		}); 	

			jQuery(function(){
			    jQuery.contextMenu({
			        selector: '#canviz div a', 
			        trigger: 'left',
			        callback: function(key, options) {
			            var m = "clicked: " + key;
			            var id = jQuery(this).attr('id');
			            window.console && console.log(m) || alert(id); 
			        },
			        items: {
			            "delete": {name: "Delete Node", icon: "delete", callback: function(key, options) {
						                    //var m = "delete was clicked";
						                    //window.console && console.log(m) || alert(m); 
						                    var id = jQuery(this).attr('id');
						                    window.location.assign("delete_node.php?nodeID=" + id+"&sessionID="+mySessionID+"&timestep="+myTimestep);
						                }},
/*			            "fold1": {
			                "name": "Edit Node", 
			                icon: "edit",
			                "items": {
						            editLabel: {
						                name: "Description", 
						                type: 'text', 
						                value: "UAV"
						            },
						            editTrust: {
						                name: "Trust Level", 
						                type: 'text', 
						                value: ".5"
						            },
						            sep4: "---------",
						            editnode: {
						                name: "Edit Node", 
						                callback: function(key, options) {
						                	var id = jQuery(this).attr('id');
							                var label = document.getElementsByName("context-menu-input-editLabel")[0].value.toString();
											var trust = document.getElementsByName("context-menu-input-editTrust")[0].value.toString();
											//alert(trust);
							                window.location.assign("edit_node.php?nodeID=" + id + "&label=" + label + "&trust=" + trust);
							            }
	            					}
			                }												
			            },*/
			            "sep1": "---------",
			            "fold1a": {
			                "name": "New Node", 
			                icon: "add",
			                "items": {
			                    "fold2": {
			                        "name": "Agent", 
			                        "items": {
			                            addAgentLabelNode: {
							                name: "From Agent Name", 
							                type: 'text', 
							                value: "Ex: Jon"
							            },
			                            addAgentLabel2Node: {
							                name: "To Agent Name", 
							                type: 'text', 
							                value: "Ex: Mary"
							            },
							            addAgentTrustNode: {
							                name: "Trust Level", 
							                type: 'text', 
							                value: "Ex: .75"
							            },
							            sep4: "---------",
							            addagentNode: {
							                name: "Add Agent", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
								                var label = document.getElementsByName("context-menu-input-addAgentLabelNode")[0].value.toString();
												var toTrust = document.getElementsByName("context-menu-input-addAgentLabel2Node")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addAgentTrustNode")[0].value.toString();
								                window.location.assign("add_node.php?type=agent&fromAgent=" + label + "&toAgent=" + toTrust + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}
		            					}
				                    }
			                    },
			                    "fold3": {
			                        "name": "Fact", 
			                        "items": {
			                        	/*addFactAgent: {
									        name: "Agent", 
									        type: 'select', 
									        options: {me: 'Me', uav: 'UAV', recon: 'Recon', informant:'Informant'}, 
								            selected: me
							        	},*/
							        	addFactAgentNode: {
									        name: "Agent", 
							                type: 'text', 
							                value: "Ex: UAV"
								        },
			                            addFactLabelNode: {
							                name: "Fact", 
							                type: 'text', 
							                value: "Ex: Increased(Gunfire)"
							            },
							            addFactTrustNode: {
							                name: "Belief Level", 
							                type: 'text', 
							                value: ".5"
						            	},
							            sep4: "---------",
							            addfactNode: {
							                name: "Add Fact", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
							                	/*var e = document.getElementsByName("context-menu-input-addFactAgent");
												var agent = e[0].options[e[0].selectedIndex].text;
							                	alert(agent);*/
							                	var agent = document.getElementsByName("context-menu-input-addFactAgentNode")[0].value.toString();
								                var label = document.getElementsByName("context-menu-input-addFactLabelNode")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addFactTrustNode")[0].value.toString();
								                window.location.assign("add_node.php?type=fact&agent=" + agent + "&belief=" + label + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}

						            	}
		            					
				                    }
			                    },
			                    "fold4": {
			                        "name": "Rule", 
			                        "items": {
			                        	/*addRuleAgent: {
									        name: "Agent", 
									        type: 'select', 
									        options: {1: 'Me', 2: 'UAV', 3: 'Recon', 4:'Informant'}, 
								            selected: 1
							        	},*/
							        	addRuleAgentNode: {
									        name: "Agent", 
							                type: 'text', 
							                value: "Ex: UAV"
								        },
			                            addRulePremiseNode: {
							                name: "Premise", 
							                type: 'text', 
							                value: "Ex: Increased(Gunfire)"
							            },
							            addRuleConclusionNode: {
							                name: "Conclusion", 
							                type: 'text', 
							                value: "Ex: NOT(Safe(Mission))"
							            },
							            addRuleTrustNode: {
							                name: "Belief Level", 
							                type: 'text', 
							                value: ".5"
						            	},
							            sep4: "---------",
							            addruleNode: {
							                name: "Add Rule", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
							                	var agent = document.getElementsByName("context-menu-input-addRuleAgentNode")[0].value.toString();
								                var premise = document.getElementsByName("context-menu-input-addRulePremiseNode")[0].value.toString();
												var conclusion = document.getElementsByName("context-menu-input-addRuleConclusionNode")[0].value.toString();
												var trust = document.getElementsByName("context-menu-input-addRuleTrustNode")[0].value.toString();
								                window.location.assign("add_node.php?type=rule&agent=" + agent + "&premise=" + premise + "&conclusion=" + conclusion + "&trust=" + trust+"&sessionID="+mySessionID+"&timestep="+myTimestep);
		            						}
		            					}
				                    }
			                    },
			                    "fold5": {
			                        "name": "Question", 
			                        "items": {
			                            addQuestionAgentNode: {
							                name: "Agent", 
							                type: 'text', 
							                value: "Ex: Recon"
							            },
							            addQuestionNode: {
							                name: "Question", 
							                type: 'text', 
							                value: "Ex: InArea(HVT)"
							            },
							            sep4: "---------",
							            addquestionNodeButton: {
							                name: "Add Question", 
							                callback: function(key, options) {
							                	//var id = jQuery(this).attr('id');
								                var agent = document.getElementsByName("context-menu-input-addQuestionAgentNode")[0].value.toString();
												var question = document.getElementsByName("context-menu-input-addQuestionNode")[0].value.toString();
								                window.location.assign("add_node.php?type=question&agent=" + agent + "&question=" + question+"&sessionID="+mySessionID+"&timestep="+myTimestep);
							            	}
		            					}
				                    }
			                    }
			                }
			            },
			            "sep": "---------",
			            "cancel": {
			                name: "Cancel", 
			                icon: "quit",
			                callback: function(key, options) {

						    }
			        }
			     }
			});
		});

    </script>
</head>
<body>

	<div class="graph">
		<div class="zoom">
			<fieldset>
				<legend>Zoom</legend>
				<input type="button" class="little_button" value="-" onclick="change_scale(-.05)" />
				<input type="button" class="little_button" value="+" onclick="change_scale(.05)" />
			</fieldset>
		</div>
		<div id="canviz"></div>
	</div>
	<div id="debug_output"></div>

<?php
	include 'right_nav.php';
?>
<script>
window.onload=function() {

    // get tab container
  	var container = document.getElementById("tabContainer");
		var tabcon = document.getElementById("tabscontent");
		//alert(tabcon.childNodes.item(1));
    // set current tab
    <?php 
	if(!array_key_exists('sessionID', $_GET) && !array_key_exists('xmlfile', $_GET)){
	    printf("var navitem = document.getElementById('tabHeader_2');");
	}else{
	    printf("var navitem = document.getElementById('tabHeader_1');");
	}
	?>
    //store which tab we are on
    var ident = navitem.id.split("_")[1];
		//alert(ident);
    navitem.parentNode.setAttribute("data-current",ident);
    //set current tab with class of activetabheader
    navitem.setAttribute("class","tabActiveHeader");

    //hide two tab contents we don't need
   	 var pages = tabcon.getElementsByTagName("div");
    	for (var i = 0; i < pages.length; i++) {
      if(pages.item(i).id.split("_")[1]!=ident) {
     	 pages.item(i).style.display="none";
      }
		};

    //this adds click event to tabs
    var tabs = container.getElementsByTagName("li");
    for (var i = 0; i < tabs.length; i++) {
      tabs[i].onclick=displayPage;
    }
}

// on click of one of tabs
function displayPage() {
  var current = this.parentNode.getAttribute("data-current");
  //remove class of activetabheader and hide old contents
  document.getElementById("tabHeader_" + current).removeAttribute("class");
  document.getElementById("tabpage_" + current).style.display="none";

  var ident = this.id.split("_")[1];
  //add class of activetabheader to new active tab and show contents
  this.setAttribute("class","tabActiveHeader");
  document.getElementById("tabpage_" + ident).style.display="block";
  this.parentNode.setAttribute("data-current",ident);
}
</script>
</body>
</html>
<?php mysqli_close($link); ?>
