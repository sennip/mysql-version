digraph g {
    graph [size="5,5", ratio=fill, overlap=false, splines=true, margin=".10"];
    node [label="\N"];
    graph [bb="0 0 3000 3000"];    
    subgraph cluster_trust_net {
        graph [bb=""];
        node [shape=circle,
            style=filled,
            fillcolor=lavender,
            fontname=arial];
        edge [color=blue];
        subgraph cluster_0 {
            graph [style="rounded,filled",
                fillcolor=whitesmoke];
<?php

/*
* Create rule node for $ruleID that can be argument ends or not
*/
$info = $my_rules[$ruleID];
if ($info["end_argument"] == 0) {
    printf("%s [label=\"%s:%s\", shape=box3d,  fontsize=50, fillcolor=lightblue, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["rule_display"], $info["level"]);
    printf("%s [label=\"%s\", shape=box, fillcolor=lightcyan, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["inference_dot_label"], $info["inference_display"]);
    printf("%s -> %s [color=darkgreen,  fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["inference_dot_label"]);
} else {
    printf("%s [label=\"%s:%s\", shape=box3d,  fontsize=50, fillcolor=lightblue, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["rule_display"], $info["level"]);
    printf("%s -> %s [color=darkgreen,  fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["inference_dot_label"]);
    if ($info["num_statuses"] == 1) {
        if($info["statuses"][0] == "IN") {
            printf("%s [label=\"%s:%s : %s\", shape=box, fontsize=50, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if ($info["statuses"][0] == "OUT") {
            printf("%s [label=\"%s:%s : %s\", fontsize=50, style=\"filled\", fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if ($info["statuses"][0] == "UNDEC") {
            printf("%s [label=\"%s:%s : %s\", fontsize=50, shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        }
    } else {
        $statuses = implode(", ", $info["statuses"]);
        printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box,  fontsize=50, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["inference_dot_label"], $info["inference_display"],
               $info["level"], $statuses);
    }
}

/*
* Create premises of $ruleID and arrows between premises and $ruleID
*/
// array of factIDs that are premises of ruleID
$my_premises_facts_ruleID = array();
// array of ruleIDs that are premises of ruleID
$my_premises_rule_ruleID = array();
foreach ($belief_arrows_to[$ruleID] as $from_id=>$arrow_info) {
    if ($arrow_info["from_rule"] == 0) {
        $my_premises_facts_ruleID[] = $from_id;
        if ($my_facts[$from_id]["end_argument"] == 0) {
            printf("%s [label=\"%s:%s\", shape=box, fillcolor=lightcyan, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $my_facts[$from_id]["dot_label"],
                   $my_facts[$from_id]["logic_display"],
                   min($my_facts[$from_id]["levels"]));
        } else {
            if ($my_facts[$from_id]["num_statuses"] == 1) {
                if ($my_facts[$from_id]["statuses"][0] == "IN") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=palegreen, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_facts[$from_id]["dot_label"],
                           $my_facts[$from_id]["logic_display"],
                           min($my_facts[$from_id]["levels"]),
                           $my_facts[$from_id]["statuses"][0]);
                }else if($my_facts[$from_id]["statuses"][0] == "OUT") {
                    printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=pink, shape=box, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_facts[$from_id]["dot_label"],
                           $my_facts[$from_id]["logic_display"],
                           min($my_facts[$from_id]["levels"]),
                           $my_facts[$from_id]["statuses"][0]);
                }else if($my_facts[$from_id]["statuses"][0] == "UNDEC") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_facts[$from_id]["dot_label"],
                           $my_facts[$from_id]["logic_display"],
                           min($my_facts[$from_id]["levels"]),
                           $my_facts[$from_id]["statuses"][0]);
                }
            } else {
                $statuses = implode(", ", $my_facts[$from_id]["statuses"]);
                printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=lemonchiffon, fontsize=50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $my_facts[$from_id]["dot_label"],
                       $my_facts[$from_id]["logic_display"],
                       min($my_facts[$from_id]["levels"]), $statuses);
            }
        }
        // Draw the arrow between fact and rule
        // NOTE: changed from grey to darkgreen
        //printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
        printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $arrow_info["from_dot_label"], $arrow_info["to_dot_label"]);
    } else {
        $my_premises_rule_ruleID[] = $from_id;
        if ($my_rules[$from_id]["end_argument"] == 0) {
            printf("%s [label=\"%s\", shape=box, fillcolor=lightcyan, fontsize = 50, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $my_rules[$from_id]["inference_dot_label"],
                   $my_rules[$from_id]["inference_display"]);
        } else {
            if ($my_rules[$from_id]["num_statuses"] == 1) {
                if($my_rules[$from_id]["statuses"][0] == "IN") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_rules[$from_id]["inference_dot_label"],
                           $my_rules[$from_id]["inference_display"],
                           $my_rules[$from_id]["level"],
                           $my_rules[$from_id]["statuses"][0]);
                } else if ($my_rules[$from_id]["statuses"][0] == "OUT") {
                    printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_rules[$from_id]["inference_dot_label"],
                           $my_rules[$from_id]["inference_display"],
                           $my_rules[$from_id]["level"],
                           $my_rules[$from_id]["statuses"][0]);
                } else if ($my_rules[$from_id]["statuses"][0] == "UNDEC") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $my_rules[$from_id]["inference_dot_label"],
                           $my_rules[$from_id]["inference_display"],
                           $my_rules[$from_id]["level"],
                           $my_rules[$from_id]["statuses"][0]);
                }
            } else {
                $statuses = implode(", ", $my_rules[$from_id]["statuses"]);
                printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $my_rules[$from_id]["inference_dot_label"],
                       $my_rules[$from_id]["inference_display"],
                       $my_rules[$from_id]["level"], $statuses);
            }
        }
        printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $arrow_info["from_dot_label"], $arrow_info["to_dot_label"]);
    }
}

                ?>
        }
<?php
/*
* Create agents nodes
*/
foreach ($agents as $agent_id => $agent_info) {
    printf("%s [label=%s, href=\"javascript:void(0)\", fillcolor=grey, onclick=\"get_id('\L', '\N')\"];\n",
           $agent_info["dot_label"], $agent_info["name"]);
}


/*
* Create fact nodes that aren't ends of arguments
*/
foreach ($my_facts_not_end_argument as $id => $info) {
    if (in_array($id, $my_premises_facts_ruleID) == FALSE) {
        printf("%s [label=\"%s:%s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["dot_label"], $info["logic_display"],
               min($info["levels"]));
    }
}

/*
* Create fact nodes that are argument conclusions
*/
foreach ($my_facts_end_argument as $id => $info) {
    if (in_array($id, $my_premises_facts_ruleID) == FALSE) {
        if ($info["num_statuses"] == 1) {
            if ($info["statuses"][0] == "IN") {
                printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $info["dot_label"], $info["logic_display"],
                       min($info["levels"]), $info["statuses"][0]);
            }else if($info["statuses"][0] == "OUT") {
                printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=grey, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $info["dot_label"], $info["logic_display"],
                       min($info["levels"]), $info["statuses"][0]);
            }else if($info["statuses"][0] == "UNDEC") {
                printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $info["dot_label"], $info["logic_display"],
                       min($info["levels"]), $info["statuses"][0]);
            }
        } else {
            $statuses = implode(", ", $info["statuses"]);
            printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $statuses);
        }
    }
}

/*
* Create rule nodes that aren't argument ends
*/
foreach ($my_rules_not_end_argument as $id=>$info) {
    if ($id != $ruleID) {
        printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"], $info["rule_display"], $info["level"]);
        if (in_array($id, $my_premises_rule_ruleID) == FALSE) {
            printf("%s [label=\"%s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"]);
        }
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"], $info["inference_dot_label"]);
    }
}

/*
* Create rule nodes that are argument conclusions
*/
foreach ($my_rules_end_argument as $id=>$info) {
    if ($id != $ruleID) {
        printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                 $info["rule_dot_label"], $info["rule_display"], $info["level"]);
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"], $info["inference_dot_label"]);
        if (in_array($id, $my_premises_rule_ruleID) == FALSE) {
            if ($info["num_statuses"] == 1) {
                if($info["statuses"][0] == "IN") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $info["inference_dot_label"], $info["inference_display"],
                           $info["level"], $info["statuses"][0]);
                } else if ($info["statuses"][0] == "OUT") {
                    printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=grey, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $info["inference_dot_label"], $info["inference_display"],
                           $info["level"], $info["statuses"][0]);
                } else if ($info["statuses"][0] == "UNDEC") {
                    printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $info["inference_dot_label"], $info["inference_display"],
                           $info["level"], $info["statuses"][0]);
                }
            } else {
                $statuses = implode(", ", $info["statuses"]);
                printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $info["inference_dot_label"], $info["inference_display"],
                       $info["level"], $statuses);
                        
            }
        }
    }
}

/*
* Create arrows between beliefs
*/
foreach ($belief_arrows as $id=>$info) {
    // YUP: both if and else arrows are the same code but
    // $info["from_dot_label"] differ and potentially we can do sthg
    // different!
	if ($info["to_id"] != $ruleID) {
		if ($info["from_rule"] == 0) {
			printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
				   $info["from_dot_label"], $info["to_dot_label"]);
		} else {
			printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
				   $info["from_dot_label"], $info["to_dot_label"]);
		}
	}
}

/*
* Create arrows for attacks (rebut and undermine)
*/
foreach ($attack_arrows as $id=>$info) {
    printf("%s -> %s [label=%s color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"],$info["to_dot_label"],$info["attack_type"]);
}

/*
* Create arrows between agents
*/
foreach ($agent_arrows as $id=>$info) {
    printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"], $info["to_dot_label"]);
}

/*
* Create arrows between agents and their direct beliefs
*/
foreach ($agent_belief_arrows as $id=>$info) {
    printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"], $info["to_dot_label"]);
    $num_agent_belief_arrows++;
}

?>
    }
}
