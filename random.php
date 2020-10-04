<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>random</title>

<script>

function byid(id) { return document.getElementById(id); }

window.onload = doit;

<?php require_once('random_server.php'); ?>

var ARR_G = [];
var ARR_C = 0;
var ARR_I = 0;

function doit() {
    var arrs = <?php echo getJSON(); ?>;
    ARR_G  = arrs; // JSON.parse(arrs);
    ARR_C  = ARR_G.length;
    pop1();
}

function pop1() {
    
    var arr = ARR_G;
    var j   = ARR_I;

    var i=0;
    var e;
    while (e = byid('e' + i)) e.innerHTML = arr[j][i++].s;

    var dateObject = new Date(arr[j].dateData.mstime);
    var	dateReadable = dateObject.toString();
    byid('date').innerHTML = 'created: ' + dateReadable + ' (+' + arr[j].dateData.uonly + 's)';
    
    const seq1 = 'seq #' + arr[j]['seq'];
    byid('seq1').innerHTML  = seq1;
    byid('seq2').innerHTML  = seq1;
    byid('seqspan').innerHTML = ' since ' + new Date(arr[j].seq_since_mstime).toString();
    byid('cset').innerHTML  = ARR_C - j + ' / ' + ARR_C;
    byid('isIP').innerHTML = (arr[j]['isIP'] ? '' : 'NOT '  ) + 'from your IP address'
    
    if (j === 0) byid('goforward').style.visibility = 'hidden';
    else         byid('goforward').style.visibility = 'visible';
    
    if (j === ARR_C - 1) byid('goback').style.visibility = 'hidden';
    else byid('goback').style.visibility = 'visible';

}

function goback() {
    if (ARR_I < ARR_C - 1) pop1(++ARR_I); 
}

function goforward() {
   if (ARR_I > 0) pop1(--ARR_I);
    
}

</script>


</head>
<body>

<div style='margin-bottom: 1ex'>
<input type='button' value='again' onclick='history.go(0)' style='font-size: 130%' />
</div>

<div id='date'></div>
<div style='margin-top: 0.2ex' >
    <span id='seq1'></span>
    <span id='isIP' ></span>
</div>


<div id='cset' style='margin-top: 2ex'></div>
<div style='padding: 0; margin-bottom: -0.5ex; margin-top: -0.5ex; font-size: 500%'>
<span onclick='goback()' id='goback'>&#8592;</span><span onclick='goforward()' id='goforward'>&#8594;</span>
</div>

<ol>

<li id='e0'></li>
<li id='e1'></li>
<li id='e2'></li>
<li id='e3'></li>
<li id='e4'></li>
<li id='e5'></li>
<li id='e6'></li>

<li id='e7' style='margin-top: 2ex'></li>
<li id='e8'></li>
<li id='e9'></li>

</ol>

<div>
    <span id='seq2'></span><span id='seqspan' style = 'font-size: 70%; opacity: 0.7'></span>
</div>

<p><a href='random.html'>original JS-only</a></p>

</body>
</html>


