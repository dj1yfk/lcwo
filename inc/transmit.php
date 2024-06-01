<div>
<h1>Transmitting practice &mdash; Send Morse Code with the Mouse Button</h1>

<p>Use the "Key" button or Space bar to send Morse code. QRS/QRQ buttons change the speed the decoder expects to decode, Clear (or Escape) removes the decoded text.</p>

<form onsubmit="return false;">
<input style="width:250px;height:250px" type="submit" value="Key" onmousedown="down();return false;" onmouseup="up();return false;" ontouchstart="down();return false;" ontouchend="up();return false;"><br>
<input type="submit" value="QRQ" onclick="changespeed(1);return false;">
<input type="submit" value="QRS" onclick="changespeed(0);return false;">
<input type="submit" value="Clear" onclick="document.getElementById('jskey').innerHTML = '&nbsp;';return false;">
</form>

<script>

var audioCtx, oscillator, biquadFilter, gainNode;

var audio_started = false;

function init_audio () {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    oscillator = audioCtx.createOscillator();
    biquadFilter = audioCtx.createBiquadFilter();
    gainNode = audioCtx.createGain();
    biquadFilter.type = "lowpass";
    biquadFilter.frequency.setValueAtTime(600, audioCtx.currentTime);
    biquadFilter.Q.setValueAtTime(15, audioCtx.currentTime);

    oscillator.type = 'sine';
    oscillator.frequency.setValueAtTime(600, audioCtx.currentTime); // value in hertz

    oscillator.connect(gainNode);
    gainNode.connect(biquadFilter);
    biquadFilter.connect(audioCtx.destination);

    oscillator.start();

    gainNode.gain.value = 0;
    audio_started = true;
}

document.onkeydown = function(evt) {
    evt = evt || window.event;
    if ("key" in evt) {
        if (evt.key === "Escape" || evt.key === "Esc") {
			document.getElementById('jskey').innerHTML = "&nbsp;";
		}
		else if (evt.key == " " || evt.ctrlKey){
            down();
		}
    } 
}

document.onkeyup= function(evt) {
    evt = evt || window.event;
    if ("key" in evt) {
		if (evt.key == " " || !evt.ctrlKey){
            up();
		}
    } 
}



</script>


<div id="speed">Speed: 8WpM
</div><br>
<div class="quoted" id="jskey">&nbsp;</div><br>

<p>This Morse decoder is highly experimental but I am glad about any
feedback or suggestions in the <a href="http://lcwo.net/forum/858/Experimental-TX-training">forum</a>.</p>
<p>If it proves to work well, it will eventually become part of a 
Morse chat function here on LCWO.net.</p>


<script>
	var time;
	var temp;
	var lastchar = "";
	var dotlength = 150;
	var avgdot = dotlength;
	var avgdash = dotlength*3;
	var idletime = new Date().getTime();
	var keydown = 0;
	var sent = 0;
	var queue = new Queue();

	var code = new Array();
	code['.-'] = "A"; code['-...'] = "B"; code['-.-.'] = "C";
	code['-..'] = "D"; code['.'] = "E"; code['..-.'] = "F";
	code['--.'] = "G"; code['....'] = "H"; code['..'] = "I";
	code['.---'] = "J"; code['-.-'] = "K"; code['.-..'] = "L";
	code['--'] = "M"; code['-.'] = "N"; code['---'] = "O";
	code['.--.'] = "P"; code['--.-'] = "Q"; code['.-.'] = "R";
	code['...'] = "S"; code['-'] = "T"; code['..-'] = "U";
	code['...-'] = "V"; code['.--'] = "W"; code['-..-'] = "X";
	code['-.--'] = "Y"; code['--..'] = "Z"; code['.----'] = "1";
	code['..---'] = "2"; code['...--'] = "3"; code['....-'] = "4";
	code['.....'] = "5"; code['-....'] = "6"; code['--...'] = "7";
	code['---..'] = "8"; code['----.'] = "9"; code['-----'] = "0";
	code['.-.-.-'] = "."; code['..--..'] = "?"; code['---...'] = ":";
	code['-....-'] = "-"; code['-.--.-'] = ")"; code['-.--.'] = "(";
	code['.-.-.'] = "+"; code['...-.-'] = "<u>SK</u>";
	code['-.-.-'] = "<u>CT</u>"; code['.--.-.'] = "@";
	code['-..-.'] = "/";
	code['--..--'] = ",";
    code['---.'] = '&Ouml;';
    code['.-.-'] = '&Auml;';
    code['..--'] = '&Uuml;';
    code['.--.-'] = '&Aring;';
    code['........'] = '<u>ERR</u>';
    code['.-...'] = '<u>AS</u>';
    code['-...-'] = '=';

	window.setInterval("checkspace();", 5*dotlength);


	function down () {
        if (!audio_started) {
            init_audio();
        }
		time = new Date().getTime();
		checkspace();
		keydown = 1;
        gainNode.gain.value = 0.1;
	}


	function up () {
		keydown = 0;
        gainNode.gain.value = 0.0;
		time = new Date().getTime() - time;
		if (time > dotlength) {
			element = "-";
			avgdash = (avgdash + time)/2;
		}
		else {
			element = ".";
			avgdot = (avgdot + time)/2;
		}
		lastchar += element;
		update();
		idletime = new Date().getTime();
	}

	function checkspace () {
		if (keydown) { return; }
		var mytime = new Date().getTime();
		var diff = mytime-idletime;

		if (diff > 1000) {
			if (queue.getlength() > 0) {
				submittext(queue.purge(), Math.round(effspeed));
			}
		}

		if (diff > 2*dotlength) {
			if (code[lastchar]) {
				append(code[lastchar], "jskey");
				queue.add(code[lastchar]);
			}
			else if (lastchar) {
				append("*", "jskey");
				queue.add('*');
			}
			lastchar = '';
			if (time-idletime > 4*dotlength) {
				append(" ", "jskey");
				queue.add(' ');
			}
		}
	}
	
	function append(what, where) {
		document.getElementById(where).innerHTML += what;
	}

	function changespeed (a) {
		if (a) {
			dotlength -= 5;	
		}
		else {
			dotlength += 5;	
		}
		update();
	}

	function update () {
		wpm = Math.round(10*1200/dotlength)/10;
		ratio = Math.round(10*avgdash / avgdot)/10;
		effspeed = Math.round(10*3600/avgdash)/10;
		var x = document.getElementById('speed');
		x.innerHTML = "Speed: "+wpm+"WpM";
		x.innerHTML += "; Ratio: " + ratio;
		x.innerHTML += "; eff. Speed: " + effspeed;
	}

	function Queue () {
		this.content = '';
		this.tmp = '';
		this.add = function (chr) {
				this.content += chr;
		}
		this.getlength = function () {
				return this.content.length;
		}
		this.purge = function () {
				this.tmp = this.content;
				this.content = '';
				return this.tmp+ ' ';
		}
	}


function submittext (text, wpm) {
}










</script>
</div>
