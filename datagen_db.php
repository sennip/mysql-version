<?php
$debug = 1;

/*
* Create agents data structure
*/
// VARS:
// $agents[id_val] = array(name=>string, dot_label=>string)
// $num_agents = int
$agents = array ();
$num_agents = 0;
$sql="SELECT DISTINCT agentID, agentName FROM agents 
         INNER JOIN agent_trust on (trustingAgent = agentID or trustedAgent = agentID) 
         where sessionID = '".$sessionID."' and timestep=".$timestep;
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $agents[$row[0]] = array("name"=>$row[1], "dot_label"=>"agent".$row[0]);
        if ($debug) {
            printf("//agents[%s] = %s\n", $row[0], $agents[$row[0]]["name"]);
        }
        $num_agents++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_agents=%d or %d\n", $num_agents, count($agents));
}

// VARS:
// $my_beliefs[id_val] = ref (beliefs = facts + rules)
// $num_my_beliefs = int
// $my_facts[id_val] = ref
// $num_my_facts = int
// $my_rules[id_val] = ref
// $num_my_rules = int
$my_beliefs = array (); $num_my_beliefs = 0;
$my_facts = array (); $num_my_facts = 0;
$my_rules = array (); $num_my_rules = 0;
/*
* Create facts data structure for agentID=1 (usually 'Me') that aren't ends of
* arguments
*/
// VARS:
// NOTE: a fact can be reached from many different agents. Hence var
// "num_paths". TODO (potential bug): There could be a case when the levels
// are the same but the fact is reached from 2 or more paths and this will not
// be caught.
// 
// $my_facts_not_end_argument[id_val] = array(predicate=>string,
//    constant=>string, is_negated=>string, logic_display=>string,
//    num_paths=>int, levels=>array(of size num_paths), is_rule=>0,
//    end_argument=>0, dot_label=>string)
// $num_my_facts_not_end_argument = int
$my_facts_not_end_argument = array ();
$num_my_facts_not_end_argument = 0;
$sql="select distinct b.beliefID, CASE               
        WHEN b.isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
        ELSE concat(p.name,'(',c.name,')') END predicate, level,
        p.name, c.name, b.isNegated
        from beliefs b
        inner join agent_has_beliefs ab on b.beliefID = ab.beliefID
        inner join predicate_has_constant pc on pc.predicateConstantID = b.conclusionID
        inner join predicates p on p.predicateID = pc.predicateID
        inner join constants c on pc.constantID = c.constantID 
        inner join arguments a on a.beliefID = b.beliefID and ab.sessionID = a.sessionID and ab.timestep=a.timestep
        inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
        where ab.agentID = 1 and b.isRule = 0 and a.isSupported = 1
        and ab.sessionID = '".$sessionID."' and ab.timestep=".$timestep."
        and b.beliefID NOT IN (select distinct b.beliefID 
                                from arguments a 
                                inner join beliefs b on b.beliefID = a.beliefID 
                                inner join parent_argument pa on pa.argumentID = a.argumentID 
                                            and pa.sessionID = a.sessionID 
                                            and pa.timestep = a.timestep 
                                where a.sessionID = '".$sessionID."' and a.timestep=".$timestep.")";
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists($row[0], $my_facts_not_end_argument)) {
            $num_paths = $my_facts_not_end_argument[$row[0]]["num_paths"];
            $my_facts_not_end_argument[$row[0]]["levels"][$num_paths] = $row[2];
            $my_facts_not_end_argument[$row[0]]["num_paths"] = $num_paths+1;
        } else {
            $my_facts_not_end_argument[$row[0]] = array ();
            $my_facts_not_end_argument[$row[0]]["dot_label"] = "fact".$row[0];
            $my_facts_not_end_argument[$row[0]]["logic_display"] = $row[1];
            $my_facts_not_end_argument[$row[0]]["num_paths"] = 1;
            $my_facts_not_end_argument[$row[0]]["levels"] = array($row[2]);
            $my_facts_not_end_argument[$row[0]]["predicate"] = $row[3];
            $my_facts_not_end_argument[$row[0]]["constant"] = $row[4];
            $my_facts_not_end_argument[$row[0]]["is_negated"] = $row[5];
            $my_facts_not_end_argument[$row[0]]["is_rule"] = 0;
            $my_facts_not_end_argument[$row[0]]["end_argument"] = 0;
            $my_facts[$row[0]] = & $my_facts_not_end_argument[$row[0]];
            $my_beliefs[$row[0]] = & $my_facts_not_end_argument[$row[0]];
            $num_my_facts_not_end_argument++;
            $num_my_facts++;
            $num_my_beliefs++;
        }
        if ($debug) {
            printf("//my_facts_not_end_argument[%s]: is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s)\n",
                   $row[0],
                   $my_facts_not_end_argument[$row[0]]["is_negated"],
                   $my_facts_not_end_argument[$row[0]]["predicate"],
                   $my_facts_not_end_argument[$row[0]]["constant"],
                   $my_facts_not_end_argument[$row[0]]["logic_display"],
                   $my_facts_not_end_argument[$row[0]]["is_rule"],
                   $my_facts_not_end_argument[$row[0]]["end_argument"],
                   $my_facts_not_end_argument[$row[0]]["num_paths"],
                   implode(", ", $my_facts_not_end_argument[$row[0]]["levels"]));
            printf("//my_facts[%s]: is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s)\n",
                   $row[0],
                   $my_facts[$row[0]]["is_negated"],
                   $my_facts[$row[0]]["predicate"],
                   $my_facts[$row[0]]["constant"],
                   $my_facts[$row[0]]["logic_display"],
                   $my_facts[$row[0]]["is_rule"],
                   $my_facts[$row[0]]["end_argument"],
                   $my_facts[$row[0]]["num_paths"],
                   implode(", ", $my_facts[$row[0]]["levels"]));
            printf("//my_beliefs[%s]: is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s)\n",
                   $row[0],
                   $my_beliefs[$row[0]]["is_negated"],
                   $my_beliefs[$row[0]]["predicate"],
                   $my_beliefs[$row[0]]["constant"],
                   $my_beliefs[$row[0]]["logic_display"],
                   $my_beliefs[$row[0]]["is_rule"],
                   $my_beliefs[$row[0]]["end_argument"],
                   $my_beliefs[$row[0]]["num_paths"],
                   implode(", ", $my_beliefs[$row[0]]["levels"]));
        }
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_my_facts_not_end_argument=%d or %d\n",
           $num_my_facts_not_end_argument, count($my_facts_not_end_argument));
    printf("//num_my_facts=%d or %d\n", $num_my_facts, count($my_facts));
    printf("//num_my_beliefs=%d or %d\n", $num_my_beliefs, count($my_beliefs));
}

/*
* Create facts data structure for agentID=1 (usually 'Me') that are argument
* conclusions
*/
// VARS:
// $my_facts_end_argument[id_val] = array (predicate=>string,
//    constant=>string, is_negated=>string, logic_display=>string,
//    is_rule=>0, end_argument=>1, dot_label=>string,
//    num_paths=>int, levels=>array(of size num_paths),
//    num_statuses=>int, statuses=>array(string))
// NOTE: statuses[] can be 'IN', 'OUT', 'UNDEC'
// $num_my_facts_end_argument = int
$my_facts_end_argument = array ();
$num_my_facts_end_argument = 0;
$sql="select distinct b.beliefID, CASE               
        WHEN b.isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
        ELSE concat(p.name,'(',c.name,')') END predicate, ab.level, max(pa.status), count(distinct pa.status) as argStatus,
        p.name, c.name, b.isNegated
        from beliefs b
        inner join agent_has_beliefs ab on b.beliefID = ab.beliefID
        inner join predicate_has_constant pc on pc.predicateConstantID = b.conclusionID
        inner join predicates p on p.predicateID = pc.predicateID
        inner join constants c on pc.constantID = c.constantID 
        inner join arguments a on a.beliefID = b.beliefID and ab.sessionID = a.sessionID and ab.timestep=a.timestep
        inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
        inner join parent_argument pa on pa.argumentID = a.argumentID and pa.sessionID = a.sessionID and pa.timestep = a.timestep 
        where ab.agentID = 1 and b.isRule = 0 and a.isSupported = 1
        and ab.sessionID = '".$sessionID."' and ab.timestep=".$timestep."
        group by b.beliefID, b.isNegated, p.name, c.name, ab.level";    
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists($row[0], $my_facts_end_argument)) {
            $num_paths = $my_facts_end_argument[$row[0]]["num_paths"];
            $my_facts_end_argument[$row[0]]["levels"][$num_paths] = $row[2];
            $my_facts_end_argument[$row[0]]["num_paths"] = $num_paths+1;
        } else {
            $my_facts_end_argument[$row[0]] = array ();
            $my_facts_end_argument[$row[0]]["dot_label"] = "fact".$row[0];
            $my_facts_end_argument[$row[0]]["logic_display"] = $row[1];
            $my_facts_end_argument[$row[0]]["num_paths"] = 1;
            $my_facts_end_argument[$row[0]]["levels"] = array($row[2]);
            // TODO: chk if statutes are updated properly. Do we have to update
            // when array_key_exists is TRUE???
            $my_facts_end_argument[$row[0]]["statuses"] = array($row[3]);
            $my_facts_end_argument[$row[0]]["num_statuses"] = $row[4];
            $my_facts_end_argument[$row[0]]["predicate"] = $row[5];
            $my_facts_end_argument[$row[0]]["constant"] = $row[6];
            $my_facts_end_argument[$row[0]]["is_negated"] = $row[7];
            $my_facts_end_argument[$row[0]]["is_rule"] = 0;
            $my_facts_end_argument[$row[0]]["end_argument"] = 1;
            $my_facts[$row[0]] = & $my_facts_end_argument[$row[0]];
            $my_beliefs[$row[0]] = & $my_facts_end_argument[$row[0]];
            $num_my_facts_end_argument++;
            $num_my_facts++;
            $num_my_beliefs++;
        }
        if($row[4] > 1) {
            $count = 0;
            $sql = "select status 
                    from parent_argument pa
                    inner join arguments a on pa.argumentID = a.argumentID and pa.sessionID = a.sessionID and a.timestep = pa.timestep
                    inner join beliefs b on a.beliefID = b.beliefID
                    where pa.sessionID = '".$sessionID."' and pa.timestep = ".$timestep." and b.beliefID = ".$row[0];
            $result2=mysqli_query($link,$sql);
            if ($result2) {
                $my_facts_end_argument[$row[0]]["statuses"] = array();
                while ($row2 = mysqli_fetch_array($result2)) {
                    $my_facts_end_argument[$row[0]]["statuses"][$count] = $row2[0];
                    $count=$count+1;
                }
                $my_facts_end_argument[$row[0]]["num_statuses"] = $count;
            }
            mysqli_free_result($result2);
        }
        if ($debug) {
            printf("//my_facts_end_argument[%s]:  is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s), num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_facts_end_argument[$row[0]]["is_negated"],
                   $my_facts_end_argument[$row[0]]["predicate"],
                   $my_facts_end_argument[$row[0]]["constant"],
                   $my_facts_end_argument[$row[0]]["logic_display"],
                   $my_facts_end_argument[$row[0]]["is_rule"],
                   $my_facts_end_argument[$row[0]]["end_argument"],
                   $my_facts_end_argument[$row[0]]["num_paths"],
                   implode(", ", $my_facts_end_argument[$row[0]]["levels"]),
                   $my_facts_end_argument[$row[0]]["num_statuses"],
                   implode(", ", $my_facts_end_argument[$row[0]]["statuses"]));
            printf("//my_facts[%s]:  is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s), num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_facts[$row[0]]["is_negated"],
                   $my_facts[$row[0]]["predicate"],
                   $my_facts[$row[0]]["constant"],
                   $my_facts[$row[0]]["logic_display"],
                   $my_facts[$row[0]]["is_rule"],
                   $my_facts[$row[0]]["end_argument"],
                   $my_facts[$row[0]]["num_paths"],
                   implode(", ", $my_facts[$row[0]]["levels"]),
                   $my_facts[$row[0]]["num_statuses"],
                   implode(", ", $my_facts[$row[0]]["statuses"]));
            printf("//my_beliefs[%s]:  is_negated=%s, pred=%s, const=%s, logic='%s', is_rule=%d, end_argument=%d, num_paths=%s, levels=(%s), num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_beliefs[$row[0]]["is_negated"],
                   $my_beliefs[$row[0]]["predicate"],
                   $my_beliefs[$row[0]]["constant"],
                   $my_beliefs[$row[0]]["logic_display"],
                   $my_beliefs[$row[0]]["is_rule"],
                   $my_beliefs[$row[0]]["end_argument"],
                   $my_beliefs[$row[0]]["num_paths"],
                   implode(", ", $my_beliefs[$row[0]]["levels"]),
                   $my_beliefs[$row[0]]["num_statuses"],
                   implode(", ", $my_beliefs[$row[0]]["statuses"]));
        }
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_my_facts_end_argument=%d or %d\n", $num_my_facts_end_argument,
           count($my_facts_end_argument));
    printf("//num_my_facts=%d or %d\n", $num_my_facts, count($my_facts));
    printf("//num_my_beliefs=%d or %d\n", $num_my_beliefs, count($my_beliefs));
}

// VARS:
// $agents_assoc_my_facts[fact_id_val] is an array of agent ids' that have
// this fact in their beliefs either directly or indirectly.
// $agents_assoc_my_facts[fact_id_val] = array(agent_ids)
// 
$agents_assoc_my_facts = array ();
foreach ($my_facts as $id=>$info) {
    $sql = "select distinct a.agentID from agents a
            inner join agent_has_beliefs ab on ab.agentID = a.agentID
            where ab.sessionID = '".$sessionID."' and ab.timestep = ".$timestep." and ab.beliefID = ".$id.";";
    $result=mysqli_query($link,$sql);
    if ($result) {
        $agents_assoc_my_facts[$id] = array ();
        while ($row = mysqli_fetch_array($result)) {
            $agents_assoc_my_facts[$id][] = $row[0];
        }
        printf("//agents_assoc_my_facts[%s] = (%s)\n", $id,
               implode(", ", $agents_assoc_my_facts[$id]));
    }
}

/*
* Create rules data structure for agentID=1 (usually 'Me') that aren't
* argument ends
*/
// VARS:
// $my_rules_not_end_argument[id_val] = array (predicate=>string,
//    constant=>string, is_negated=>string, inference_display=>string,
//    level=>float, is_rule = 1, end_argument = 0, num_premises=>int,
//    premises=>array(predicate=>string, constant=>string,
//                    is_negated=>string, logic_display=>string),
//    premises_display=>string, rule_display=>string,
//    inference_dot_label=>string, rule_dot_label=>string)
// $num_my_rules_not_end_argument = int
$my_rules_not_end_argument = array ();
$num_my_rules_not_end_argument = 0;
$sql="select distinct b.beliefID, CASE 
        WHEN b.isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
        ELSE concat(p.name,'(',c.name,')') END predicate, level,
        p.name, c.name, b.isNegated
        from beliefs b
        inner join agent_has_beliefs ab on b.beliefID = ab.beliefID  
        inner join predicate_has_constant pc on pc.predicateConstantID = b.conclusionID
        inner join predicates p on p.predicateID = pc.predicateID
        inner join constants c on pc.constantID = c.constantID 
        inner join arguments a on a.beliefID = b.beliefID and ab.sessionID = a.sessionID and ab.timestep=a.timestep
        inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
        where ab.agentID = 1 and b.isRule = 1 and a.isSupported=1
        and ab.sessionID = '".$sessionID."' and ab.timestep=".$timestep."
        and b.beliefID NOT IN (select distinct b.beliefID 
                                from arguments a 
                                inner join beliefs b on b.beliefID = a.beliefID 
                                inner join parent_argument pa on pa.argumentID = a.argumentID 
                                            and pa.sessionID = a.sessionID 
                                            and pa.timestep = a.timestep 
                                where a.sessionID = '".$sessionID."' and a.timestep=".$timestep.")";
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $my_rules_not_end_argument[$row[0]] = array();
        $my_rules_not_end_argument[$row[0]]["inference_dot_label"] = "inference".$row[0];
        $my_rules_not_end_argument[$row[0]]["rule_dot_label"] = "rule".$row[0];
        $my_rules_not_end_argument[$row[0]]["inference_display"] = $row[1];
        $my_rules_not_end_argument[$row[0]]["level"] = $row[2];
        $my_rules_not_end_argument[$row[0]]["predicate"] = $row[3];
        $my_rules_not_end_argument[$row[0]]["constant"] = $row[4];
        $my_rules_not_end_argument[$row[0]]["is_negated"] = $row[5];
        $my_rules_not_end_argument[$row[0]]["is_rule"] = 1;
        $my_rules_not_end_argument[$row[0]]["end_argument"] = 0;
        $my_rules_not_end_argument[$row[0]]["num_premises"] = 0;
        $my_rules_not_end_argument[$row[0]]["premises"] = array();
        $sql="select CASE 
            WHEN isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
            ELSE concat(p.name,'(',c.name,')') END predicate,
            p.name, c.name, b.isNegated
            from belief_has_premises b
            inner join predicate_has_constant pc on pc.predicateConstantID = b.premiseID
            inner join predicates p on p.predicateID = pc.predicateID
            inner join constants c on pc.constantID = c.constantID 
            where beliefID = ".$row[0].";";
        $return=mysqli_query($link,$sql);
        $premise='';
        $count=0;
        while($innerrow = mysqli_fetch_array($return)) {
            $my_rules_not_end_argument[$row[0]]["premises"][count]["logic_display"]=$innerrow[0];
            $my_rules_not_end_argument[$row[0]]["premises"][count]["predicate"]=$innerrow[1];
            $my_rules_not_end_argument[$row[0]]["premises"][count]["constant"]=$innerrow[2];
            $my_rules_not_end_argument[$row[0]]["premises"][count]["is_negated"]=$innerrow[3];
              if($count > 0){ $premise .= ", "; }
              $premise .= $innerrow[0];
              $count++;
        }
        mysqli_free_result($return);
        $my_rules_not_end_argument[$row[0]]["num_premises"] = $count;
        $my_rules_not_end_argument[$row[0]]["premises_display"] = $premise;
        $my_rules_not_end_argument[$row[0]]["rule_display"] = $row[1]." :- ".$premise;
        $my_rules[$row[0]] = & $my_rules_not_end_argument[$row[0]];
        $my_beliefs[$row[0]] = & $my_rules_not_end_argument[$row[0]];
        if ($debug) {
            printf("//my_rules_not_end_argument[%s]:, inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s\n",
                   $row[0],
                   $my_rules_not_end_argument[$row[0]]["is_negated"],
                   $my_rules_not_end_argument[$row[0]]["predicate"],
                   $my_rules_not_end_argument[$row[0]]["constant"],
                   $my_rules_not_end_argument[$row[0]]["num_premises"],
                   $my_rules_not_end_argument[$row[0]]["rule_display"],
                   $my_rules_not_end_argument[$row[0]]["is_rule"],
                   $my_rules_not_end_argument[$row[0]]["end_argument"],
                   $my_rules_not_end_argument[$row[0]]["level"]);
            printf("//my_rules[%s]:, inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s\n",
                   $row[0],
                   $my_rules[$row[0]]["is_negated"],
                   $my_rules[$row[0]]["predicate"],
                   $my_rules[$row[0]]["constant"],
                   $my_rules[$row[0]]["num_premises"],
                   $my_rules[$row[0]]["rule_display"],
                   $my_rules[$row[0]]["is_rule"],
                   $my_rules[$row[0]]["end_argument"],
                   $my_rules[$row[0]]["level"]);
            printf("//my_beliefs[%s]:, inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s\n",
                   $row[0],
                   $my_beliefs[$row[0]]["is_negated"],
                   $my_beliefs[$row[0]]["predicate"],
                   $my_beliefs[$row[0]]["constant"],
                   $my_beliefs[$row[0]]["num_premises"],
                   $my_beliefs[$row[0]]["rule_display"],
                   $my_beliefs[$row[0]]["is_rule"],
                   $my_beliefs[$row[0]]["end_argument"],
                   $my_beliefs[$row[0]]["level"]);
        }
        $num_my_rules_not_end_argument++;
        $num_my_rules++;
        $num_my_beliefs++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_my_rules_not_end_argument=%d or %d\n",
           $num_my_rules_not_end_argument, count($my_rules_not_end_argument));
    printf("//num_my_rules=%d or %d\n", $num_my_rules, count($my_rules));
    printf("//num_my_beliefs=%d or %d\n", $num_my_beliefs, count($my_beliefs));
}

/*
* Create rules data structure for agentID=1 (usually 'Me') that are argument
* conclusions
*/
// VARS:
// $my_rules_end_argument[id_val] = array (predicate=>string,
//    constant=>string, is_negated=>string, inference_display=>string,
//    is_rule = 1, end_argument = 1, level=>float, num_premises=>int,
//    premises=>array(predicate=>string, constant=>string,
//                    is_negated=>string, logic_display=>string),
//    premises_display=>string, rule_display=>string,
//    num_statuses=>int, statuses=>array(string),
//    inference_dot_label=>string, rule_dot_label=>string)
// NOTE: statuses[] can be 'IN', 'OUT', 'UNDEC'
// $num_my_rules_end_argument = int
$my_rules_end_argument = array ();
$num_my_rules_end_argument = 0;
$sql="select distinct b.beliefID, CASE               
        WHEN b.isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
        ELSE concat(p.name,'(',c.name,')') END predicate, ab.level,
        max(pa.status), count(distinct pa.status) as argStatus,
        p.name, c.name, b.isNegated
        from beliefs b
        inner join agent_has_beliefs ab on b.beliefID = ab.beliefID
        inner join predicate_has_constant pc on pc.predicateConstantID = b.conclusionID
        inner join predicates p on p.predicateID = pc.predicateID
        inner join constants c on pc.constantID = c.constantID 
        inner join arguments a on a.beliefID = b.beliefID and ab.sessionID = a.sessionID and ab.timestep=a.timestep
        inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
        inner join parent_argument pa on pa.argumentID = a.argumentID and pa.sessionID = a.sessionID and pa.timestep = a.timestep 
        where ab.agentID = 1 and b.isRule = 1 and a.isSupported = 1
        and ab.sessionID = '".$sessionID."' and ab.timestep=".$timestep."
        group by b.beliefID, b.isNegated, p.name, c.name, ab.level";
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $my_rules_end_argument[$row[0]] = array();
        $my_rules_end_argument[$row[0]]["inference_dot_label"] = "inference".$row[0];
        $my_rules_end_argument[$row[0]]["rule_dot_label"] = "rule".$row[0];
        $my_rules_end_argument[$row[0]]["inference_display"] = $row[1];
        $my_rules_end_argument[$row[0]]["level"] = $row[2];
        $my_rules_end_argument[$row[0]]["statuses"] = array($row[3]);
        $my_rules_end_argument[$row[0]]["num_statuses"] = $row[4];
        $my_rules_end_argument[$row[0]]["predicate"] = $row[5];
        $my_rules_end_argument[$row[0]]["constant"] = $row[6];
        $my_rules_end_argument[$row[0]]["is_negated"] = $row[7];
        $my_rules_end_argument[$row[0]]["is_rule"] = 1;
        $my_rules_end_argument[$row[0]]["end_argument"] = 1;
        $my_rules_end_argument[$row[0]]["num_premises"] = 0;
        $my_rules_end_argument[$row[0]]["premises"] = array();
        $sql="select CASE 
              WHEN isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
              ELSE concat(p.name,'(',c.name,')') END predicate,
              p.name, c.name, b.isNegated
              from belief_has_premises b
              inner join predicate_has_constant pc on pc.predicateConstantID = b.premiseID
              inner join predicates p on p.predicateID = pc.predicateID
              inner join constants c on pc.constantID = c.constantID 
              where beliefID = ".$row[0].";";
        $return=mysqli_query($link,$sql);
        $premise='';
        $count=0;
        while($innerrow = mysqli_fetch_array($return)) {
            $my_rules_end_argument[$row[0]]["premises"][count]["logic_display"]=$innerrow[0];
            $my_rules_end_argument[$row[0]]["premises"][count]["predicate"]=$innerrow[1];
            $my_rules_end_argument[$row[0]]["premises"][count]["constant"]=$innerrow[2];
            $my_rules_end_argument[$row[0]]["premises"][count]["is_negated"]=$innerrow[3];
            if($count > 0){ $premise .= ", ";}
            $premise .= $innerrow[0];    
            $count++;
        }
        mysqli_free_result($return);
        $my_rules_end_argument[$row[0]]["num_premises"] = $count;
        $my_rules_end_argument[$row[0]]["premises_display"] = $premise;
        $my_rules_end_argument[$row[0]]["rule_display"] = $row[1]." :- ".$premise;
          
        if($row[4] > 1) {
            $count = 0;
            $sql = "select status 
                    from parent_argument pa
                    inner join arguments a on pa.argumentID = a.argumentID and pa.sessionID = a.sessionID and a.timestep = pa.timestep
                    inner join beliefs b on a.beliefID = b.beliefID
                    where pa.sessionID = '".$sessionID."' and pa.timestep = ".$timestep." and b.beliefID = ".$row[0];
            $result2=mysqli_query($link,$sql);
            if ($result2) {
                $my_rules_end_argument[$row[0]]["statuses"] = array();
                while ($row2 = mysqli_fetch_array($result2)) {
                    $my_rules_end_argument[$row[0]]["statuses"][$count] = $row2[0];
                    $count=$count+1;
                }
                $my_rules_end_argument[$row[0]]["num_statuses"] = $count;
            }
            mysqli_free_result($result2);
        }
        $my_rules[$row[0]] = & $my_rules_end_argument[$row[0]];
        $my_beliefs[$row[0]] = & $my_rules_end_argument[$row[0]];
        if ($debug) {
            printf("//my_rules_end_argument[%s]: inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s, num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_rules_end_argument[$row[0]]["is_negated"],
                   $my_rules_end_argument[$row[0]]["predicate"],
                   $my_rules_end_argument[$row[0]]["constant"],
                   $my_rules_end_argument[$row[0]]["num_premises"],
                   $my_rules_end_argument[$row[0]]["rule_display"],
                   $my_rules_end_argument[$row[0]]["is_rule"],
                   $my_rules_end_argument[$row[0]]["end_argument"],
                   $my_rules_end_argument[$row[0]]["level"],
                   $my_rules_end_argument[$row[0]]["num_statuses"],
                   implode(", ", $my_rules_end_argument[$row[0]]["statuses"]));
            printf("//my_rules[%s]: inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s, num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_rules[$row[0]]["is_negated"],
                   $my_rules[$row[0]]["predicate"],
                   $my_rules[$row[0]]["constant"],
                   $my_rules[$row[0]]["num_premises"],
                   $my_rules[$row[0]]["rule_display"],
                   $my_rules[$row[0]]["is_rule"],
                   $my_rules[$row[0]]["end_argument"],
                   $my_rules[$row[0]]["level"],
                   $my_rules[$row[0]]["num_statuses"],
                   implode(", ", $my_rules[$row[0]]["statuses"]));
            printf("//my_beliefs[%s]: inference(is_negated=%s, pred=%s, const=%s), num_premises=%d, rule_display='%s', is_rule=%d, end_argument=%d, level=%s, num_statuses=%d, statuses=(%s)\n",
                   $row[0],
                   $my_beliefs[$row[0]]["is_negated"],
                   $my_beliefs[$row[0]]["predicate"],
                   $my_beliefs[$row[0]]["constant"],
                   $my_beliefs[$row[0]]["num_premises"],
                   $my_beliefs[$row[0]]["rule_display"],
                   $my_beliefs[$row[0]]["is_rule"],
                   $my_beliefs[$row[0]]["end_argument"],
                   $my_beliefs[$row[0]]["level"],
                   $my_beliefs[$row[0]]["num_statuses"],
                   implode(", ", $my_beliefs[$row[0]]["statuses"]));
        }
        $num_my_rules_end_argument++;
        $num_my_rules++;
        $num_my_beliefs++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_my_rules_end_argument=%d or %d\n",
           $num_my_rules_end_argument, count($my_rules_end_argument));
    printf("//num_my_rules=%d or %d\n", $num_my_rules, count($my_rules));
    printf("//num_my_beliefs=%d or %d\n", $num_my_beliefs, count($my_beliefs));
}

/*
* Create data structure for arrows between beliefs
*/
// VARS:
// $belief_arrows[fromID_toID] = array(from_id=>string, from_dot_label=>string,
//     from_rule = int (1 or 0), from_ref=<ref>, to_id=>string,
//     to_dot_label=>string, to_ref=<ref>)
// $num_belief_arrows = int
// $belief_arrows_from[fromID] = array(toID=>ref)
// $num_belief_arrows_from = int
// $belief_arrows_to[toID] = array(fromID=>ref)
// $num_belief_arrows_to = int
$belief_arrows = array();
$num_belief_arrows = 0;
$belief_arrows_from = array();
$num_belief_arrows_from = 0;
$belief_arrows_to = array();
$num_belief_arrows_to = 0;
$sql="select distinct case when b1.isRule = 1 then concat('inference',a.beliefID) else concat('fact',a.beliefID) end fromID, 
    case when b2.isRule = 1 then concat('rule',ab.beliefID) else concat('fact',ab.beliefID) end toID,
    b1.isRule, a.beliefID, ab.beliefID
    from arguments a
    inner join arguments ab on a.supportsArgumentID = ab.argumentID  and a.sessionID = ab.sessionID and a.timestep=ab.timestep
    inner join beliefs b1 on a.beliefID = b1.beliefID 
    inner join beliefs b2 on ab.beliefID = b2.beliefID
    inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
    inner join questions q2 on q2.sessionID = ab.sessionID and q2.timestep = ab.timestep and q2.isSupported = ab.isSupported
        where a.isSupported = 1 and ab.isSupported = 1
    and a.sessionID = '".$sessionID."' and a.timestep=".$timestep;
// TODO: what about checking that q.conclusionID = a.beliefID
//    order by a.questionID, a.supportsArgumentID";
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $from_to = $row[3]."_".$row[4];
        $belief_arrows[$from_to]["from_dot_label"] = $row[0];
        $belief_arrows[$from_to]["to_dot_label"] = $row[1];
        $belief_arrows[$from_to]["from_rule"] = $row[2];
        $belief_arrows[$from_to]["from_id"] = $row[3];
        $belief_arrows[$from_to]["to_id"] = $row[4];
        if ($row[2] == 0) {
            if (array_key_exists($row[3], $my_facts)) {
                $belief_arrows[$from_to]["from_ref"] = & $my_facts[$row[3]];
            }
            else
            {
                printf("ERROR: cannot find fact %s\n", $row[3]);
                // TODO: else exit??
            }
        } else {
            if (array_key_exists($row[3], $my_rules)) {
                $belief_arrows[$from_to]["from_ref"] = & $my_rules[$row[3]];
            }
            else
            {
                printf("ERROR: cannot find rule %s\n", $row[3]);
                // TODO: else exit??
            }
        }
        if (array_key_exists($row[4], $my_rules)) {
            $belief_arrows[$from_to]["to_ref"] = & $my_rules[$row[4]];
        }
        else
        {
            printf("ERROR: cannot find rule %s\n", $row[4]);
            // TODO: else exit??
        }
        if (array_key_exists($row[3], $belief_arrows_from)) {
            $belief_arrows_from[$row[3]][$row[4]] = & $belief_arrows[$from_to];
        } else {
            $belief_arrows_from[$row[3]] = array($row[4] => & $belief_arrows[$from_to]);
            $num_belief_arrows_from++;
        }
        if (array_key_exists($row[4], $belief_arrows_to)) {
            $belief_arrows_to[$row[4]][$row[3]] = & $belief_arrows[$from_to];
        } else {
            $belief_arrows_to[$row[4]] = array($row[3] => & $belief_arrows[$from_to]);
            $num_belief_arrows_to++;
        }
        if ($debug) {
            printf("//%s: %s(%s) -> %s(%s)\n", $from_to,
                   $belief_arrows[$from_to]["from_dot_label"],
                   ($belief_arrows[$from_to]["from_rule"] == 0)?$belief_arrows[$from_to]["from_ref"]["logic_display"]:$belief_arrows[$from_to]["from_ref"]["inference_display"],
                   $belief_arrows[$from_to]["to_dot_label"],
                   $belief_arrows[$from_to]["to_ref"]["inference_display"]);
        }
        $num_belief_arrows++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_belief_arrows=%d or %d\n", $num_belief_arrows,
           count($belief_arrows));
    foreach($belief_arrows_from as $from => $tos) {
        printf("//From(%s): (%s)\n", $from, implode(", ", array_keys($tos)));
    }
    printf("//num_belief_arrows_from=%d or %d\n", $num_belief_arrows_from,
           count($belief_arrows_from));
    foreach($belief_arrows_to as $to => $froms) {
        printf("//To(%s): (%s)\n", $to, implode(", ", array_keys($froms)));
    }
    printf("//num_belief_arrows_to=%d or %d\n", $num_belief_arrows_to,
           count($belief_arrows_to));
}

/*
* Create data structure for arrows for attacks (rebut and undermine)
*/
// VARS:
// $attack_arrows[fromID_toID] = array (from_id=>string, from_dot_label=>string,
//    from_rule = int (0 or 1 if rule), from_ref=ref, to_id=>string,
//    to_dot_label=>string, to_rule = int (0 or 1 if rule), to_ref = ref,
//    attack_type = string ("rebut" or "undermine"))
// $num_attack_arrows = int
$attack_arrows = array ();
$num_attack_arrows = 0;
$sql="select distinct case when b.isRule = 1 then concat('inference',b.beliefID)
                  else concat('fact',b.beliefID) END fromID,
             case when b2.isRule = 1 then concat('inference',b2.beliefID)
                  else concat('fact',b2.beliefID) END toID, paa.attackType, 
             case when b.isRule = 1 and b2.isRule = 1 then 'rebut'
                   when b.isRule = 1 and b2.isRule = 0 then 'undermine'
                   when b.isRule = 0 and b2.isRule = 0 then 'undermine'
                   ELSE 'rebut' END attackTypeOld,
              b.isRule, b.beliefID, b2.isRule, b2.beliefID
              from parent_argument_attacks_argument paa
        inner join parent_argument pa1 on pa1.parentArgumentID = paa.fromParentArgID
        inner join arguments a on a.argumentID = pa1.argumentID and a.sessionID = pa1.sessionID and a.timestep = pa1.timestep
        inner join beliefs b on b.beliefID = a.beliefID
        inner join parent_argument pa2 on pa2.parentArgumentID = paa.toParentArgID
        inner join arguments a2 on a2.argumentID = pa2.argumentID and a2.sessionID = pa2.sessionID and a2.timestep = pa2.timestep
        inner join beliefs b2 on b2.beliefID = a2.beliefID
        where a.sessionID = '".$sessionID."' and a.timestep = ".$timestep;
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $from_to = $row[5]."_".$row[7];
        $attack_arrows[$from_to]["from_dot_label"] = $row[0];
        $attack_arrows[$from_to]["to_dot_label"] = $row[1];
        $attack_arrows[$from_to]["attack_type"] = $row[2];
        $attack_arrows[$from_to]["from_rule"] = $row[4];    
        $attack_arrows[$from_to]["from_id"] = $row[5];    
        $attack_arrows[$from_to]["to_rule"] = $row[6];    
        $attack_arrows[$from_to]["to_id"] = $row[7];    
        if ($row[4] == 1) {
            if (array_key_exists($row[5], $my_rules)) {
                $attack_arrows[$from_to]["from_ref"] = & $my_rules[$row[5]];
            }
            // TODO: else exit???
        } else {
            if (array_key_exists($row[5], $my_facts)) {
                $attack_arrows[$from_to]["from_ref"] = & $my_facts[$row[5]];
            }
            // TODO: else exit???
        }
        if ($row[6] == 1) {
            if (array_key_exists($row[7], $my_rules)) {
                $attack_arrows[$from_to]["to_ref"] = & $my_rules[$row[7]];
            }
            // TODO: else exit???
        } else {
            if (array_key_exists($row[7], $my_facts)) {
                $attack_arrows[$from_to]["to_ref"] = & $my_facts[$row[7]];
            }
            // TODO: else exit???
        }
        if ($debug) {
            printf("//%s: %s(%s) -> %s(%s)\n",
                   $attack_arrows[$from_to]["attack_type"],
                   $attack_arrows[$from_to]["from_dot_label"],
                   ($attack_arrows[$from_to]["from_rule"] == 0)?$attack_arrows[$from_to]["from_ref"]["logic_display"]:$attack_arrows[$from_to]["from_ref"]["inference_display"],
                   $attack_arrows[$from_to]["to_dot_label"],
                   ($attack_arrows[$from_to]["to_rule"] == 0)?$attack_arrows[$from_to]["to_ref"]["logic_display"]:$attack_arrows[$from_to]["to_ref"]["inference_display"]);
        }
        $num_attack_arrows++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_attack_arrows=%d or %d\n", $num_attack_arrows,
           count($attack_arrows));
}

/*
* Create data structure for arrows between agents
*/
// VARS:
// $agent_arrows[fromID_toID] = array(from_id=>string, from_dot_label=>string,
//     from_ref=ref, to_id=>string, to_dot_label=>string, to_ref=>ref,
//     level=>string)
// $num_agent_arrows = int
// $agent_arrows_from[fromID] = array(toID=>ref)
// $num_agent_arrows_from = int
// $agent_arrows_to[toID] = array(fromID=>ref)
// $num_agent_arrows_to = int
$agent_arrows = array();
$num_agent_arrows = 0;
$agent_arrows_from = array();
$num_agent_arrows_from = 0;
$agent_arrows_to = array();
$num_agent_arrows_to = 0;
$sql="select concat('agent',trustingAgent), concat('agent',trustedAgent),
          trustingAgent, trustedAgent, level
          from agent_trust where sessionID = '".$sessionID."' and timestep=".$timestep;
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $from_to = $row[2]."_".$row[3];
        $agent_arrows[$from_to]["from_dot_label"] = $row[0];
        $agent_arrows[$from_to]["to_dot_label"] = $row[1];
        $agent_arrows[$from_to]["from_id"] = $row[2];
        $agent_arrows[$from_to]["to_id"] = $row[3];
        $agent_arrows[$from_to]["level"] = $row[4];
        if (array_key_exists($row[2], $agents)) {
            $agent_arrows[$from_to]["from_ref"] = & $agents[$row[2]];    
        }
        // TODO: else exit???
        if (array_key_exists($row[3], $agents)) {
            $agent_arrows[$from_to]["to_ref"] = & $agents[$row[3]];
        }
        if (array_key_exists($row[2], $agent_arrows_from)) {
            $agent_arrows_from[$row[2]][$row[3]] = & $agent_arrows[$from_to];
        } else {
            $agent_arrows_from[$row[2]] = array($row[3] => & $agent_arrows[$from_to]);
            $num_agent_arrows_from++;
        }
        if (array_key_exists($row[3], $agent_arrows_to)) {
            $agent_arrows_to[$row[3]][$row[2]] = & $agent_arrows[$from_to];
        } else {
            $agent_arrows_to[$row[3]] = array($row[2] => & $agent_arrows[$from_to]);
            $num_agent_arrows_to++;
        }
        // TODO: else exit???
        if ($debug) {
            printf("//%s(%s:%s) -> %s(%s:%s): level=%s\n",
                   $agent_arrows[$from_to]["from_dot_label"],
                   $agent_arrows[$from_to]["from_id"],
                   $agent_arrows[$from_to]["from_ref"]["name"],
                   $agent_arrows[$from_to]["to_dot_label"],
                   $agent_arrows[$from_to]["to_id"],
                   $agent_arrows[$from_to]["to_ref"]["name"],
                   $agent_arrows[$from_to]["level"]);
        }
        $num_agent_arrows++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_agent_arrows=%d or %d\n", $num_agent_arrows,
           count($agent_arrows));
    foreach ($agent_arrows_from as $from => $tos) {
        printf("//From(%s): (%s)\n", $from, implode(", ", array_keys($tos)));
    }
    printf("//num_agent_arrows_from=%d or %d\n", $num_agent_arrows_from,
           count($agent_arrows_from));
    foreach ($agent_arrows_to as $to => $froms) {
        printf("//To(%s): (%s)\n", $to, implode(", ", array_keys($froms)));
    }
    printf("//num_agent_arrows_to=%d or %d\n", $num_agent_arrows_to,
           count($agent_arrows_to));
}

/*
* Create data structure for arrows between agents and their direct beliefs
*/
// VARS:
// $agent_belief_arrows[fromID_toID] = array(from_id=>string,
///    from_dot_label=>string, from_ref=>ref, to_id=>string, to_dot_label=>string, 
//     to_rule=>0 or 1, to_ref=>ref, level=>string)
// $num_agent_belief_arrows = int
// $agent_belief_arrows_from[fromID] = array(toID=>ref)
// $num_agent_belief_arrows_from = int
// $agent_belief_arrows_to[toID] = array(fromID=>ref)
// $num_agent_belief_arrows_to = int
$agent_belief_arrows = array();
$num_agent_belief_arrows = 0;
$agent_belief_arrows_from = array();
$num_agent_belief_arrows_from = 0;
$agent_belief_arrows_to = array();
$num_agent_belief_arrows_to = 0;
$sql="select distinct concat('agent',ab.agentID),
    case when isRule = 1 then concat('rule',b.beliefID) else concat('fact',b.beliefID) end l,
    ab.agentID, isRule, b.beliefID, ab.level
    from agent_has_beliefs ab
    inner join beliefs b on ab.beliefID = b.beliefID 
    inner join arguments a on a.beliefID = b.beliefID  and a.sessionID = ab.sessionID and a.timestep=ab.timestep
    inner join questions q on q.sessionID = a.sessionID and q.timestep = a.timestep and q.isSupported = a.isSupported
    where isInferred = 0 and a.isSupported = 1 and b.isRule = 0
    and a.sessionID = '".$sessionID."' and a.timestep=".$timestep;
$result=mysqli_query($link,$sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $from_to = $row[2]."_".$row[4];
        $agent_belief_arrows[$from_to]["from_dot_label"] = $row[0];
        $agent_belief_arrows[$from_to]["to_dot_label"] = $row[1];
        $agent_belief_arrows[$from_to]["from_id"] = $row[2];
        $agent_belief_arrows[$from_to]["to_rule"] = $row[3];
        $agent_belief_arrows[$from_to]["to_id"] = $row[4];
        $agent_belief_arrows[$from_to]["level"] = $row[5];
        if (array_key_exists($row[2], $agents)) {
            $agent_belief_arrows[$from_to]["from_ref"] = & $agents[$row[2]];    
        } else {
            printf("//ERROR: cannot find agent %s\n", $row[2]);
            // TODO: else exit???
        }
        // TODO: just use my_beliefs
        if ($row[3] == 1) {
            if (array_key_exists($row[4], $my_rules)) {
                $agent_belief_arrows[$from_to]["to_ref"] = & $my_rules[$row[4]];    
            }
            // TODO: else exit???
        } else {
            if (array_key_exists($row[4], $my_facts)) {
                $agent_belief_arrows[$from_to]["to_ref"] = & $my_facts[$row[4]];    
            }
            // TODO: else exit???
        }
        if (array_key_exists($row[2], $agent_belief_arrows_from)) {
            $agent_belief_arrows_from[$row[2]][$row[4]] = & $agent_belief_arrows[$from_to];
        } else {
            $agent_belief_arrows_from[$row[2]] = array($row[4] => & $agent_belief_arrows[$from_to]);
            $num_agent_belief_arrows_from++;
        }
        if (array_key_exists($row[4], $agent_belief_arrows_to)) {
            $agent_belief_arrows_to[$row[4]][$row[2]] = & $agent_belief_arrows[$from_to];
        } else {
            $agent_belief_arrows_to[$row[4]] = array($row[2] => & $agent_belief_arrows[$from_to]);
            $num_agent_belief_arrows_to++;
        }
        if ($debug) {
            printf ("//%s(%s:%s) -> %s(%s:%s): level=%s\n",
                    $agent_belief_arrows[$from_to]["from_dot_label"],
                    $agent_belief_arrows[$from_to]["from_id"],
                    $agent_belief_arrows[$from_to]["from_ref"]["name"],
                    $agent_belief_arrows[$from_to]["to_dot_label"],
                    $agent_belief_arrows[$from_to]["to_id"],
                    ($agent_belief_arrows[$from_to]["to_rule"] == 1)?$agent_belief_arrows[$from_to]["to_ref"]["inference_display"]:$agent_belief_arrows[$from_to]["to_ref"]["logic_display"],
                    $agent_belief_arrows[$from_to]["level"]);
        }
        $num_agent_belief_arrows++;
    }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_agent_belief_arrows=%d or %d\n", $num_agent_belief_arrows,
           count($agent_belief_arrows));
    foreach ($agent_belief_arrows_from as $from => $tos) {
        printf("//From(%s): (%s)\n", $from, implode(", ", array_keys($tos)));
    }
    printf("//num_agent_belief_arrows_from=%d or %d\n",
           $num_agent_belief_arrows_from, count($agent_belief_arrows_from));
    foreach ($agent_belief_arrows_to as $to => $froms) {
        printf("//To(%s): (%s)\n", $to, implode(", ", array_keys($froms)));
    }
    printf("//num_agent_belief_arrows_to=%d or %d\n",
           $num_agent_belief_arrows_to, count($agent_belief_arrows_to));
}

/*
* Create data structure for arguments.
* Find agentIDs and beliefIDs for arguments
*/
// VARS:
// $arguments[id_val] = array(level=>string, status=>string,
//     conclusion_display=>string, num_agentIDs=>int, agentIDs=>array(strings),
//     num_beliefIDs=>int, beliefIDs=>array(strings))
// $num_arguments = int
$arguments = array ();
$num_arguments = 0;

$sql = "select pa.parentArgumentID, pa.level, pa.status, CASE               
        WHEN b.isNegated=1 THEN concat('NOT(',p.name,'(',c.name,'))') 
        ELSE concat(p.name,'(',c.name,')') END predicate
from parent_argument pa
inner join arguments a on a.argumentID = pa.argumentID and a.timestep = pa.timestep and pa.sessionID = a.sessionID
inner join beliefs b on b.beliefID = a.beliefID
inner join predicate_has_constant pc on pc.predicateConstantID = b.conclusionID
inner join predicates p on p.predicateID = pc.predicateID
inner join constants c on pc.constantID = c.constantID 
where pa.sessionID = '".$sessionID."' and pa.timestep=".$timestep." and a.isSupported = 1;";
$result=mysqli_query($link,$sql);
if ($result) {
  while ($row = mysqli_fetch_array($result)) {
    $arguments[$row[0]] = array ();
    $arguments[$row[0]]["level"] = $row[1];
    $arguments[$row[0]]["status"] = $row[2];
    $arguments[$row[0]]["conclusion_display"] = $row[3];
    if ($debug) {
        printf ("//argument(%s): level=%s, status=%s, conclusion_display='%s', ",
                $row[0], $arguments[$row[0]]["level"],
                $arguments[$row[0]]["status"],
                $arguments[$row[0]]["conclusion_display"]);
    }
    $arguments[$row[0]]["num_agentIDs"] = 0;
    $arguments[$row[0]]["agentIDs"] = array ();
    $i = 0;
    // Get all agentIDs associated with this argument
    $sql = "select distinct ab.agentID
           from parent_argument pa
           inner join parent_argument_has_argument paa on pa.parentArgumentID = paa.parentArgumentID
           inner join arguments a on paa.argumentID = a.argumentID and pa.sessionID = a.sessionID and pa.timestep = a.timestep
           inner join agent_has_beliefs ab on a.beliefID = ab.beliefID and ab.sessionID = a.sessionID and ab.timestep = a.timestep
           where pa.sessionID = '".$sessionID."' and pa.timestep = ".$timestep." and pa.parentArgumentID = ".$row[0];
    $result_agent=mysqli_query($link, $sql);
    if ($result_agent) {
        while ($row_agent = mysqli_fetch_array($result_agent)) {
            $arguments[$row[0]]["agentIDs"][$i] = $row_agent[0];
            $i++;
        }
        $arguments[$row[0]]["num_agentIDs"] = $i;
    }
    mysqli_free_result($result_agent);
    if ($debug) {
        printf ("num_agentIDs=%d, agentIDs=(%s), ",
                $arguments[$row[0]]["num_agentIDs"],
                implode(", ", $arguments[$row[0]]["agentIDs"]));
    }

    $arguments[$row[0]]["num_beliefIDs"] = 0;
    $arguments[$row[0]]["beliefIDs"] = array ();
    $i = 0;
    // Get all beliefIDs associated with this argument
    $sql = "select distinct a.beliefID
           from parent_argument pa
           inner join parent_argument_has_argument paa on pa.parentArgumentID = paa.parentArgumentID
           inner join arguments a on paa.argumentID = a.argumentID and pa.sessionID = a.sessionID and pa.timestep = a.timestep
           where pa.sessionID = '".$sessionID."' and pa.timestep = ".$timestep." and pa.parentArgumentID = ".$row[0];
    $result_belief=mysqli_query($link, $sql);
    if ($result_belief) {
        while ($row_belief = mysqli_fetch_array($result_belief)) {
            $arguments[$row[0]]["beliefIDs"][$i] = $row_belief[0];
            $i++;
        }
        $arguments[$row[0]]["num_beliefIDs"] = $i;
    }
    mysqli_free_result($result_belief);
    if ($debug) {
        printf ("num_beliefIDs=%d, beliefIDs=(%s)\n",
                $arguments[$row[0]]["num_beliefIDs"],
                implode(", ", $arguments[$row[0]]["beliefIDs"]));
    }

    $num_arguments++;
  }
}
mysqli_free_result($result);
if ($debug) {
    printf("//num_arguments=%d or %d\n", $num_arguments, count($arguments));
}

$store = array ();
$store["agents"] = & $agents; $store["num_agents"] = $num_agents;
$store["my_beliefs"] = & $my_beliefs;
$store["num_my_beliefs"] = $num_my_beliefs;
$store["my_facts"] = & $my_facts; $store["num_my_facts"] = $num_my_facts;
$store["my_rules"] = & $my_rules; $store["num_my_rules"] = $num_my_rules;
$store["my_facts_not_end_argument"] = & $my_facts_not_end_argument;
$store["num_my_facts_not_end_argument"] = $num_my_facts_not_end_argument;
$store["my_facts_end_argument"] = & $my_facts_end_argument;
$store["num_my_facts_end_argument"] = $num_my_facts_end_argument;
$store["agents_assoc_my_facts"] = $agents_assoc_my_facts;
$store["my_rules_not_end_argument"] = & $my_rules_not_end_argument;
$store["num_my_rules_not_end_argument"] = $num_my_rules_not_end_argument;
$store["my_rules_end_argument"] = & $my_rules_end_argument;
$store["num_my_rules_end_argument"] = $num_my_rules_end_argument;
$store["belief_arrows"] = & $belief_arrows;
$store["num_belief_arrows"] = $num_belief_arrows;
$store["belief_arrows_from"] = & $belief_arrows_from;
$store["num_belief_arrows_from"] = & $num_belief_arrows_from;
$store["belief_arrows_to"] = & $belief_arrows_to;
$store["num_belief_arrows_to"] = $num_belief_arrows_to;
$store["attack_arrows"] = & $attack_arrows;
$store["num_attack_arrows"] = $num_attack_arrows;
$store["agent_arrows"] = & $agent_arrows;
$store["num_agent_arrows"] = $num_agent_arrows;
$store["agent_arrows_from"] = & $agent_arrows_from;
$store["num_agent_arrows_from"] = $num_agent_arrows_from;
$store["agent_arrows_to"] = & $agent_arrows_to;
$store["num_agent_arrows_to"] = $num_agent_arrows_to;
$store["agent_belief_arrows"] = & $agent_belief_arrows;
$store["num_agent_belief_arrows"] = $num_agent_belief_arrows;
$store["agent_belief_arrows_from"] = & $agent_belief_arrows_from;
$store["num_agent_belief_arrows_from"] = $num_agent_belief_arrows_from;
$store["agent_belief_arrows_to"] = & $agent_belief_arrows_to;
$store["num_agent_belief_arrows_to"] = $num_agent_belief_arrows_to;
$store["arguments"] = & $arguments; $store["num_arguments"] = $num_arguments;

/*
if ($debug) {
    printf("//arguments keys: %s\n",
          implode(", ", array_keys($store["arguments"])));
}
*/

$fp = file_put_contents("graphs2/".$sessionID.".vars",  serialize($store));
if ($fp == FALSE) {
    printf("//ERROR: on writing variables to file\n");
    // TODO: exit
}

?>
