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
    var arrs = <?php echo web_random::getJSON(); ?>;
    ARR_G  = arrs;
    ARR_C  = ARR_G.length;
    pop1();
}

function pop1() {
    
    var arr = ARR_G;
    var j   = ARR_I;

    var i=0;
    var e;
    while (e = byid('e' + i)) e.innerHTML = arr[j]['rand'][i++].s;

    byid('date').innerHTML = 'created: ' + arr[j].dtime + ' (+' + arr[j].dhronly + 's, core #' + arr[j].pid + ')';
    
    const seq1 = 'seq #' + arr[j]['seq'];
    byid('seq1').innerHTML  = seq1;
    byid('seq2').innerHTML  = seq1;
    byid('seqspan').innerHTML = ' since ' + new Date(arr[j].seq_since_mstime).toString();
    byid('cset').innerHTML  = ARR_C - j + ' / ' + ARR_C;
    byid('isIP').innerHTML = (arr[j]['isIP'] ? '' : 'NOT '  ) + 'from your IP addr'
    
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

<style>
.arp { 
	display: inline-block;
	height: 1.5em;
	position: relative;
}

.arrows {  
	display: inline-block; 
	font-size: 10vh;
	line-height: 0.3em; /*  v-a bottom or even top or text-top is tempting for desktop, but don't do it because it messes up mobile. *//* vertical-align: text-top; */
	margin-right: 0.2ex;
	position: relative;
	top: -0.0ex;
}

.seqd { 
	margin-top:    0.2ex;
	margin-bottom: 0.0ex; 
}

#l10 { 
	margin-top: 0; 
	position: relative; 
	font-size: 2.95vh;
	padding-left: 1.5em;
}

.gp10 {
	position: relative;
	font-size: 0pt;
}
/*
@media only screen and (max-height: 1290px) {
	.arp { 
		position: relative;
		top: -1.5ex;
		margin-bottom: 0; 
		margin-top: 0ex;
		display: inline-block;
		line-height: 1.0em;
	}
	
	#l10 {
		margin-top: 0;
		position: relative;
		top: -2ex;
		
	}

}
*/
</style>
</head>
<body>
<div style='margin-bottom: 1ex'>
<input type='button' value='again' onclick='history.go(0)' style='font-size: 130%' />
</div>

<div id='date'></div>
<div class='seqd' >
    <span id='cset' style='padding-left: 0ex; font-size: 110%; font-weight: bold'></span>
    <span id='isIP' style='padding-left: 0.2em' ></span>
    <span id='seq1'></span>
</div>
	<div class='gp10'>
<div class='arp'>
	<span onclick='goback()'    id='goback'    class='arrows'>&#8592;</span><!-- no whitespace allowed!!!
	--><span onclick='goforward()' id='goforward' class='arrows'>&#8594;</span>
</div>

<ol id='l10'>

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

	</div>

<div>
    <span id='seq2'></span><span id='seqspan' style = 'font-size: 70%; opacity: 0.7'></span>
</div>

<p><a href='https://github.com/kwynncom/random-number-web-begin-2018' 
      style='padding-right: 3.5em'>source code</a><a href='random.html'>original JS-only</a></p>

</body>
</html>


