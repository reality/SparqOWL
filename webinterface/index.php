<?php
$query = $_GET["query"];
$ontology = $_GET["ontology"];
$type = $_GET["type"];
if ($type == null) { 
  $type = "subclass" ;
}

// OWL TYPE <endpoint> <ontURI> { query }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Aber-OWL: SPARQL</title>
	<link href="../css/smoothness/jquery-ui-1.10.4.custom.css" rel="stylesheet">
	<link href="../css/dataTables.jqueryui.css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.js"></script>  
        <script src="../js/jquery-ui-1.10.4.custom.js"></script>  
        <script src="../js/jquery.autosize.js"></script>  
	<script src="../js/jquery.dataTables.js"></script>
	<style>
	table.display {
  	  	margin: 0 auto;
  		width: 100%;
  		clear: both;
  		border-collapse: collapse;
		table-layout: fixed;         // add this 
		word-wrap:break-word;        // add this 
	}
	table{
		font: 80% "Trebuchet MS", sans-serif;
		margin: 80px;
	}
	body{
		font: 100% "Trebuchet MS", sans-serif;
		margin: 80px;
	}
	.menubar {
	  position: fixed;
	  top: 0;
	  left: 0;
	  z-index: 999;
	  width: 95%;
	}
	.menubarleft {
	  position: fixed;
	  top: 0;
	  left: 20px;
	  z-index: 999;
	  width: 95%;
	  display: none;
	}
	.demoHeaders {
		margin-top: 2em;
	}
	#icons {
		margin: 0;
		padding: 0;
	}
	.display thead {
	}
	#icons li {
		margin: 2px;
		position: relative;
		padding: 4px 0;
		cursor: pointer;
		float: left;
		list-style: none;
	}
	#icons span.ui-icon {
		float: left;
		margin: 0 4px;
	}
	.fakewindowcontain .ui-widget-overlay {
		position: absolute;
	}
        #radio {
            padding: 4px;
            display: inline-block;
        }
	.title {
		text-align: center;
		margin: 0px;
	}
#right {
  margin-left:  2%;
  vertical-align: middle;
}
#left {
  float: left;
width: 65%;
}
	</style>

<script language="javascript">
  var example1 = "PREFIX GO: <http://purl.uniprot.org/go/>\n\
PREFIX taxon:<http://purl.uniprot.org/taxonomy/>\n\
PREFIX up: <http://purl.uniprot.org/core/>\n\
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>\n\
\n\
SELECT DISTINCT ?pname ?protein ?label ?ontid WHERE { \n\
  ##############################################\n\
  # binds ?ontid to the results of the OWL query \n\
  VALUES ?ontid { \n\
    OWL subclass <http://jagannath.pdn.cam.ac.uk/aber-owl/service/> <>\n\
      { part_of some 'apoptotic process' }\n\
  } . \n\
  ##############################################\n\
  # ?ontid is now bound to the set of class IRIs of the OWL query \n\
        ?protein a up:Protein .\n\
        ?protein up:organism taxon:9606 .\n\
        ?protein up:mnemonic ?pname .\n\
        ?protein up:classifiedWith ?ontid .\n\
        ?ontid skos:prefLabel ?label .\n\
\n\
}";

var example2 = "PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n\
PREFIX gc:<http://purl.org/gwas/schema#>\n\
PREFIX xsd:<http://www.w3.org/2001/XMLSchema#>\n\
PREFIX obo:<http://www.obofoundry.org/ro/ro.owl#>\n\
\n\
SELECT ?gene ?ext_marker_id ?pvalue ?ontid\n\
WHERE\n\
{\n\
    GRAPH ?g\n\
    {\n\
        ?marker gc:associated ?phenotype ;\n\
        gc:locatedInGene ?gene ;\n\
        gc:pvalue ?pvalue;\n\
        obo:hasSynonym ?ext_marker_id.\n\
        ?phenotype gc:hpoAnnotation ?ontid .\n\
    }\n\
    FILTER (xsd:float(?pvalue) <= 1e-10) .\n\
    FILTER ( \n\
    ?ontid IN ( OWL subclass <http://jagannath.pdn.cam.ac.uk/aber-owl/service/> <http://purl.obolibrary.org/obo/hp.owl>\n\
      { arrhythmia }\n\
         )\n\
    ) . \n\
}";


  function change( type ) {
    var ta = document.getElementById("ta");
    var hf = document.getElementById("hiddenfield");
    if (type == 'values') {
      hf.value = "values";
      ta.value = "SELECT ?s ?p ?o WHERE { \n" +
	"  ##############################################\n" +
	"  # binds ?ontid to the results of the OWL query \n" +
	"  VALUES ?ontid { \n" +
	"    OWL <?php echo $type; ?> <http://jagannath.pdn.cam.ac.uk/aber-owl/service/> <<?php echo $ontology; ?>>\n" +
	"      { <?php echo $query; ?> }\n" +
	"  } . \n" +
	"  ##############################################\n" +
	"  # ?ontid is bound to the set of class IRIs of the OWL query \n"+
	"  # enter query here\n" +
	"}\n";

    } else {
      hf.value = "filter";
      ta.value = "SELECT * WHERE {\n" +
      "  ##############################################\n" +
      "  # enter query here \n\n" +
      "  ##############################################\n" +
      "  # filter on ?x \n" +
      "  FILTER ( \n" +
      "    ?x IN ( OWL <?php echo $type; ?> <http://jagannath.pdn.cam.ac.uk/aber-owl/service/> <<?php echo $ontology; ?>>\n" +
      "      { <?php echo $query; ?> }\n" +
      "    )\n" +
      "  ) . \n" +
      "}\n";

    }
  }
$(document).ready(function() { 
    change('values');
    //    $('#ta').autosize() ;
    $( "#radio" ).buttonset();
    $("#ex1").click(function() {
	document.getElementById('ta').value=example1;
	document.getElementById('endpoint').value='http://beta.sparql.uniprot.org/';
	document.getElementById('short').checked = true;
	document.getElementById('hiddenfield').value='values';
	$('#radio1').button('enable');
	$('#radio1').attr('checked', true);
	$('#radio2').button('disable');
	$('#radio').buttonset('refresh');
    });
    $("#ex2").click(function() {
	document.getElementById('ta').value=example2;
	document.getElementById('endpoint').value='http://fuseki.gwascentral.org/gc/query';
	document.getElementById('short').checked = true;
	document.getElementById('hiddenfield').value='filter';
	$('#radio1').button('disable');
	$('#radio2').attr('checked', true);
	$('#radio2').button('enable');
	$('#radio').buttonset('refresh');
    });
} );
  </script>
</head>

<body>
  	<p class="menubar" align="right"><small><a href="help.html">Help</a></small></p>
	<h1 class="title" title="Framework for ontology-based data access">Aber-OWL: SPARQL</h1>
  <br /><br />
    <form name="sparqowl" action="sparqowl.php" method="POST">
  <p>
  <div id="radio">
  <input onclick="javascript:change('values');" type="radio" id="radio1" name="radioa" value="values" checked="checked"><label for="radio1">Use in VALUES statement</label>
  <input onclick="javascript:change('filter');" type="radio" id="radio2" name="radiob" value="filter" style="float:right;"><label for="radio2">Use in FILTER statement</label>
  </div>
  </p>
  <div id="container">
  <div id="left">
  <p>
  <textarea id="ta" name="sparqlquery" cols="100" rows="15">
  </textarea>
  </p>
  <p>SPARQL Endpoint URI: <input type="text" size="100%" name="endpoint" id="endpoint"/></p>
  <p>Use OBO-style URIs: <input type="checkbox" name="short" id="short"/></p>
  <input type="hidden" id="hiddenfield" name="qtype" value="values" /></p>
  <p><input type="submit" /></p>
  </div>
  <div id="right">
  <br />
  <p><a href="#" id="ex1">Example 1: Find all human proteins associated with a 'part of apoptosis' in UniProt</a>
  </p>
  <p><a href="#" id="ex2">Example 2: Search GWAS Central for genes and markers significantly involved in arrhythmia</a>
  </p>
  </div>
  </div>

    </form>
</body>

</html>