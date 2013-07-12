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
$arg_agentIDs = $arguments[$argumentID]["agentIDs"];
$agentIDs = array_keys($agents);
$not_arg_agentIDs = array_diff($agentIDs, $arg_agentIDs);
//printf("//arg_agentIDs = (%s), not_arg_agentIDs = (%s)\n",
//       implode(", ", $arg_agentIDs), implode(", ", $not_arg_agentIDs));

$arg_beliefIDs = $arguments[$argumentID]["beliefIDs"];
$beliefIDs = array_keys($my_beliefs);
$not_arg_beliefIDs = array_diff($beliefIDs, $arg_beliefIDs);
//printf("//arg_beliefIDs = (%s), not_arg_beliefIDs = (%s)\n",
//       implode(", ", $arg_beliefIDs), implode(", ", $not_arg_beliefIDs));

/*
* Create agents nodes that are part of this argument (argumentID)
*/
foreach ($arg_agentIDs as $id) {
      printf("%s [label=%s, fontsize=35, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $agents[$id]["dot_label"], $agents[$id]["name"]);
}

/*
* Create fact nodes that aren't ends of arguments and that are part of
* this argument (argumentID).
*/
foreach ($arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 0) &&
        ($my_beliefs[$id]["end_argument"] == 0)) {
        printf("%s [label=\"%s:%s\", shape=box, fontsize=35, fillcolor=lightcyan, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["dot_label"],
               $my_beliefs[$id]["logic_display"],
               min($my_beliefs[$id]["levels"]));
    }
}

/*
* Create fact nodes that are argument conclusions and that are part of
* this argument (argumentID)
*/
foreach ($arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 0) &&
        ($my_beliefs[$id]["end_argument"] == 1)) {
        $info = & $my_beliefs[$id];
        if (($info["num_statuses"] == 1) && ($info["statuses"][0] == "IN")) {
            printf("%s [label=\"%s:%s : %s\", fontsize=35,shape=box, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "OUT")) {
            printf("%s [label=\"%s:%s : %s\", fontsize=35,style=\"filled\", fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "UNDEC")) {
            printf("%s [label=\"%s:%s : %s\", fontsize=35,shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else {
            printf("%s [label=\"%s:%s : %s\", fontsize=35, style=\"dotted, filled\" shape=box, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), implode(", ", $info["statuses"]));
        }
    }
}

/*
* Create rule nodes that aren't argument ends and that are part of this
* argument (argumentID)
*/
foreach ($arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 1) &&
        ($my_beliefs[$id]["end_argument"] == 0)) {
        printf("%s [label=\"%s:%s\", shape=box3d, fontsize=35, fillcolor=lightblue, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["rule_dot_label"],
               $my_beliefs[$id]["rule_display"], $my_beliefs[$id]["level"]);
        printf("%s [label=\"%s\", shape=box, fontsize=35, fillcolor=lightcyan, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["inference_dot_label"],
               $my_beliefs[$id]["inference_display"]);
        printf("%s -> %s [color=darkgreen, fontsize=35, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["rule_dot_label"],
               $my_beliefs[$id]["inference_dot_label"]);
    }
}

/*
* Create rule nodes that are argument conclusions and that are part of
* this argument (argumentID)
*/
foreach ($arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 1) &&
        ($my_beliefs[$id]["end_argument"] == 1)) {
        $info = & $my_beliefs[$id];
        printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=lightblue, fontsize=35, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"],
               $info["rule_display"], $info["level"]);
        printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"],
               $info["inference_dot_label"]);

        if (($info["num_statuses"] == 1) && ($info["statuses"][0] == "IN")) {
            printf("%s [label=\"%s:%s : %s\", shape=box, fontsize=35, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "OUT")) {
            printf("%s [label=\"%s:%s : %s\", style=\"filled\", fontsize=35, fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "UNDEC")) {
            printf("%s [label=\"%s:%s : %s\", shape=box, fontsize=35, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else {
            printf("%s [label=\"%s:%s : %s\", style=\"dotted, fontsize=35, filled\" shape=box, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"],
                   implode(", ", $info["statuses"]));
        }
    }
}

/*
* Create arrows between beliefs that are part of this argument
* (argumentID)
*/
$arg_belief_arrows = array();
foreach ($arg_beliefIDs as $id1) {
    foreach ($arg_beliefIDs as $id2) {
        if ($id1 != $id2) {
            $from_to = $id1."_".$id2;
            if (array_key_exists($from_to, $belief_arrows)) {
                $arg_belief_arrows[$from_to] = "Yes";
                $info = $belief_arrows[$from_to];
                if ($info["from_rule"] == 0) {
                    printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $info["from_dot_label"], $info["to_dot_label"]);
                } else {
                    printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                           $info["from_dot_label"], $info["to_dot_label"]);
                }
            }
        }
    }
}

/*
* Create arrows for attacks (rebut and undermine) that are part of this
* argument (argumentID)
*/
$arg_attack_arrows = array();
foreach ($arg_beliefIDs as $id1) {
    foreach ($arg_beliefIDs as $id2) {
        if ($id1 != $id2) {
            $from_to = $id1."_".$id2;
            if (array_key_exists($from_to, $attack_arrows)) {
                $arg_attack_arrows[$from_to] = "Yes";
                $info = $attack_arrows[$from_to];
                printf("%s -> %s [label=%s color=orange, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $info["from_dot_label"],$info["to_dot_label"],$info["attack_type"]);
            }
        }
    }
}

/*
* Create arrows between agents that are part of this argument
* (argumentID)
*/
$arg_agent_arrows = array ();
foreach ($arg_agentIDs as $id1) {
    foreach ($arg_agentIDs as $id2) {
        if ($id1 != $id2) {
            $from_to = $id1."_".$id2;
            if (array_key_exists($from_to, $agent_arrows)) {
                $arg_agent_arrows[$from_to] = "Yes";
                printf("%s -> %s [color=yellow, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                       $agent_arrows[$from_to]["from_dot_label"],
                       $agent_arrows[$from_to]["to_dot_label"]);
            }
        }
    }
}

/*
* Create arrows between agents and their direct beliefs that are part of
* this argument (argumentID)
*/
$arg_agent_belief_arrows = array();
foreach ($arg_agentIDs as $id1) {
    foreach ($arg_beliefIDs as $id2) {
        $from_to = $id1."_".$id2;
        if (array_key_exists($from_to, $agent_belief_arrows)) {
            $arg_agent_belief_arrows[$from_to] = "Yes";
            printf("%s -> %s [color=crimson, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $agent_belief_arrows[$from_to]["from_dot_label"],
                   $agent_belief_arrows[$from_to]["to_dot_label"]);
        }
    }
}

//////////////////////////////////////////////////////////////////
?>
        }

<?php
/*
* Create agents nodes that are NOT part of this argument (argumentID)
*/
foreach ($not_arg_agentIDs as $id) {
    printf("%s [label=%s, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $agents[$id]["dot_label"], $agents[$id]["name"]);
}

/*
* Create fact nodes that aren't ends of arguments and that are NOT part of
* this argument (argumentID).
*/
foreach ($not_arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 0) &&
        ($my_beliefs[$id]["end_argument"] == 0)) {
        printf("%s [label=\"%s:%s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["dot_label"],
               $my_beliefs[$id]["logic_display"],
               min($my_beliefs[$id]["levels"]));
    }
}

/*
* Create fact nodes that are argument conclusions and that are NOT part of
* this argument (argumentID)
*/
foreach ($not_arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 0) &&
        ($my_beliefs[$id]["end_argument"] == 1)) {
        $info = & $my_beliefs[$id];
        if (($info["num_statuses"] == 1) && ($info["statuses"][0] == "IN")) {
            printf("%s [label=\"%s:%s : %s\",shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "OUT")) {
            printf("%s [label=\"%s:%s : %s\",style=\"filled\", fillcolor=grey, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "UNDEC")) {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        } else {
            printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), implode(", ", $info["statuses"]));
        }
    }
}

/*
* Create rule nodes that aren't argument ends and that are NOT part of 
* this argument (argumentID)
*/
foreach ($not_arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 1) &&
        ($my_beliefs[$id]["end_argument"] == 0)) {
        printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["rule_dot_label"],
               $my_beliefs[$id]["rule_display"], $my_beliefs[$id]["level"]);
        printf("%s [label=\"%s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["inference_dot_label"],
               $my_beliefs[$id]["inference_display"]);
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $my_beliefs[$id]["rule_dot_label"],
               $my_beliefs[$id]["inference_dot_label"]);
    }
}

/*
* Create rule nodes that are argument conclusions and that are NOT part of
* this argument (argumentID)
*/
foreach ($not_arg_beliefIDs as $id) {
    if (($my_beliefs[$id]["is_rule"] == 1) &&
        ($my_beliefs[$id]["end_argument"] == 1)) {
        $info = & $my_beliefs[$id];
        printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"],
               $info["rule_display"], $info["level"]);
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["rule_dot_label"],
               $info["inference_dot_label"]);

        if (($info["num_statuses"] == 1) && ($info["statuses"][0] == "IN")) {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "OUT")) {
            printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=grey, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if (($info["num_statuses"] == 1) &&
                   ($info["statuses"][0] == "UNDEC")) {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else {
            printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"],
                   implode(", ", $info["statuses"]));
        }
    }
}

/*
* Create arrows between beliefs that are NOT part of this argument
* (argumentID)
*/
foreach ($belief_arrows as $id=>$info) {
    if (array_key_exists($id, $arg_belief_arrows) == FALSE) {
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
* Create arrows for attacks (rebut and undermine) that are NOT part of this
* argument (argumentID)
*/
foreach ($attack_arrows as $id=>$info) {
    if (array_key_exists($id, $arg_attack_arrows) == FALSE) {
        printf("%s -> %s [label=%s color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["from_dot_label"], $info["to_dot_label"],
               $info["attack_type"]);
    }
}

/*
* Create arrows between agents that are NOT part of this argument
* (argumentID)
*/
foreach ($agent_arrows as $id=>$info) {
    if (array_key_exists($id, $arg_agent_arrows) == FALSE) {
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["from_dot_label"], $info["to_dot_label"]);
    }
}

/*
* Create arrows between agents and their direct beliefs that are NOT part of
* this argument (argumentID)
*/
foreach ($agent_belief_arrows as $id=>$info) {
    if (array_key_exists($id, $arg_agent_belief_arrows) == FALSE) {
        printf("%s -> %s [color=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["from_dot_label"], $info["to_dot_label"]);
    }
}

?>
    }
}
