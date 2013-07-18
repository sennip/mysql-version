digraph g {
    graph [size="5,5", ratio=fill, overlap=false, splines=true, margin=".10"];
    node [label="\N"];
    graph [bb="0 0 3000 3000"];    
  subgraph cluster_trust_net {
    graph [bb="", style="filled, rounded", fillcolor=whitesmoke];
    node [shape=circle,
      style=filled,
      fillcolor=lavender,
      fontname=arial];
    edge [color=blue];
<?php
/** @page dotgen_hw_impl Implementation Details: dotgen_hw.php
 * dotgen_hw.php: 
 * File that generates dot file containing the "Scenario Overview" with all
 * arguments (outcomes).
 *
 * Generates dot file containing the "Scenario Overview" starting from "Me"
 * with all generated arguments using data structures defined in file
 * datagen_db.php.
 */


/** @page dotgen_hw_impl
 *
 * * Create agents nodes
 */
foreach ($agents as $agent_id => $agent_info) {
    printf("%s [label=%s, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $agent_info["dot_label"], $agent_info["name"]);
}

/** @page dotgen_hw_impl
 *
 * * Create fact nodes for agentID=1 (usually 'Me') that aren't ends of
 * arguments
 */
foreach ($my_facts_not_end_argument as $id => $info) {
    printf("%s [label=\"%s:%s\", shape=box, fillcolor=lightcyan, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["dot_label"], $info["logic_display"], min($info["levels"]));
}

/** @page dotgen_hw_impl
 *
 * * Create fact nodes for agentID=1 (usually 'Me') that are argument
 * conclusions
 */
foreach ($my_facts_end_argument as $id => $info) {
    if ($info["num_statuses"] == 1) {
        if ($info["statuses"][0] == "IN") {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        }else if($info["statuses"][0] == "OUT") {
            printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        }else if($info["statuses"][0] == "UNDEC") {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["dot_label"], $info["logic_display"],
                   min($info["levels"]), $info["statuses"][0]);
        }
    } else {
        $statuses = implode(", ", $info["statuses"]);
        printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["dot_label"], $info["logic_display"],
               min($info["levels"]), $statuses);
    }
}

/** @page dotgen_hw_impl
 *
 * * Create rule nodes for agentID=1 (usually 'Me') that aren't argument ends
 */
foreach ($my_rules_not_end_argument as $id=>$info) {
    printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=lightblue, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["rule_display"], $info["level"]);
    printf("%s [label=\"%s\", shape=box, fillcolor=lightcyan, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["inference_dot_label"], $info["inference_display"]);
    printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["inference_dot_label"]);
}

/** @page dotgen_hw_impl
 *
 * * Create rule nodes for agentID=1 (usually 'Me') that are argument
 * conclusions
 */
foreach ($my_rules_end_argument as $id=>$info) {
    printf("%s [label=\"%s:%s\", shape=box3d, fillcolor=lightblue, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
             $info["rule_dot_label"], $info["rule_display"], $info["level"]);
    printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["rule_dot_label"], $info["inference_dot_label"]);
    if ($info["num_statuses"] == 1) {
        if($info["statuses"][0] == "IN") {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=palegreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if ($info["statuses"][0] == "OUT") {
            printf("%s [label=\"%s:%s : %s\", style=\"filled\", fillcolor=pink, shape=box, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        } else if ($info["statuses"][0] == "UNDEC") {
            printf("%s [label=\"%s:%s : %s\", shape=box, fillcolor=grey, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
                   $info["inference_dot_label"], $info["inference_display"],
                   $info["level"], $info["statuses"][0]);
        }
    } else {
        $statuses = implode(", ", $info["statuses"]);
        printf("%s [label=\"%s:%s : %s\", style=\"dotted, filled\" shape=box, fillcolor=lemonchiffon, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["inference_dot_label"], $info["inference_display"],
               $info["level"], $statuses);
                
    }
}

/** @page dotgen_hw_impl
 *
 * * Create arrows between beliefs
 */
foreach ($belief_arrows as $id=>$info) {
    // YUP: both if and else arrows are the same code but
    // $info["from_dot_label"] differ and potentially we can do sthg
    // different!
    if ($info["from_rule"] == 0) {
        printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["from_dot_label"], $info["to_dot_label"]);
    } else {
        printf("%s -> %s [color=darkgreen, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
               $info["from_dot_label"], $info["to_dot_label"]);
    }
}

/** @page dotgen_hw_impl
 *
 * * Create arrows for attacks (rebut and undermine)
 */
foreach ($attack_arrows as $id=>$info) {
    printf("%s -> %s [label=%s color=orange, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"],$info["to_dot_label"],$info["attack_type"]);
}

/** @page dotgen_hw_impl
 *
 * * Create arrows between agents
 */
foreach ($agent_arrows as $id=>$info) {
    printf("%s -> %s [color=yellow, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"], $info["to_dot_label"]);
}

/** @page dotgen_hw_impl
 *
 * * Create arrows between agents and their direct beliefs
 */
foreach ($agent_belief_arrows as $id=>$info) {
    printf("%s -> %s [color=crimson, href=\"javascript:void(0)\", onclick=\"get_id('\L', '\N')\"];\n",
           $info["from_dot_label"], $info["to_dot_label"]);
    $num_agent_belief_arrows++;
}

?>
}
}
