<h1>LCWO statistics</h1>

<p>Monthly statistics of user activity on LCWO.net. The value for the current month is extrapolated.</p>

<h2 id="accounts">User accounts</h2>
<table><tr><td>
<canvas id="signups" width="1000" height="300"></canvas>
</td><td>
<div id="signups_stat">yo</div>
<input type="checkbox" onclick="plot('signups', this.checked);"> Cumulative
</td></tr></table>

<h2 id="lesson">Koch lessons</h2>
<table><tr><td>
<canvas id="koch" width="1000" height="300"></canvas>
</td><td>
<div id="koch_stat"></div>
<input type="checkbox" onclick="plot('koch', this.checked);"> Cumulative
</td></tr></table>

<h2 id="codegroups">Codegroups sessions</h2>
<table><tr><td>
<canvas id="groups" width="1000" height="300"></canvas>
</td><td>
<div id="groups_stat"></div>
<input type="checkbox" onclick="plot('groups', this.checked);"> Cumulative
</td></tr></table>

<h2 id="plaintext">Plaintext sessions</h2>
<table><tr><td>
<canvas id="plaintext" width="1000" height="300"></canvas>
</td><td>
<div id="plaintext_stat"></div>
<input type="checkbox" onclick="plot('plaintext', this.checked);"> Cumulative
</td></tr></table>

<h2 id="callsigns">Callsign training sessions</h2>
<table><tr><td>
<canvas id="callsigns" width="1000" height="300"></canvas>
</td><td>
<div id="callsigns_stat"></div>
<input type="checkbox" onclick="plot('callsigns', this.checked);"> Cumulative
</td></tr></table>

<h2 id="words">Word training sessions</h2>
<table><tr><td>
<canvas id="words" width="1000" height="300"></canvas>
</td><td>
<div id="words_stat"></div>
<input type="checkbox" onclick="plot('words', this.checked);"> Cumulative
</td></tr></table>

<h2 id="qtc">QTC training sessions</h2>
<table><tr><td>
<canvas id="qtc" width="1000" height="300"></canvas>
</td><td>
<div id="qtc_stat"></div>
<input type="checkbox" onclick="plot('qtc', this.checked);"> Cumulative
</td></tr></table>

<script>

function plot(item, cumul) {
   var request =  new XMLHttpRequest();
   request.open("GET", "/api/index.php?action=stats&item=" + item, true);
   request.onreadystatechange = function() {
       var done = 4, ok = 200;
       if (request.readyState == done && request.status == ok) {
           var data = JSON.parse(request.responseText);

           var c = document.getElementById(item);
           var ctx = c.getContext("2d");
           var w = c.width;
           var h = c.height;

           // find maximum and sum of all values;
           // also: for the last month extrapolate
           // to the full month and remember how
           // much we extrapolated (to show it
           // in another color)

           var max = 0;
           var mon_max = 0;
           var mon_max_date = "";
           var sum = 0;
           var ext = 0;
           for (var i = 0; i < data.length; i++) {

               // current month
               if (i == data.length - 1) {
                   // current day
                   var d = new Date().getDate();
                   var orig = parseInt(data[i]['count']);
                   data[i]['count'] = orig * 30/d;
                   var ext = data[i]['count'] - orig;
               }

               if (parseInt(data[i]['count']) > max) {
                   max = parseInt(data[i]['count']);
                   mon_max_date = data[i]['date'];
               }
               sum += parseInt(data[i]['count']);
           }

           mon_max = max;

           if (cumul) {
               max = sum;
           }


           ctx.fillStyle = '#eeeeee';
           ctx.fillRect(0,0, w, h);

           ctx.save();
           ctx.translate(0, h);
           ctx.scale(w/data.length, h * -1 / max);

           // plot scale
           var scale_step = 0;
           if (max > 1000000) {
               scale_step = 500000;
           }
           else if (max > 300000) {
               scale_step = 100000;
           }
           else if (max > 30000) {
               scale_step = 10000;
           }
           else if (max > 10000) {
               scale_step = 5000;
           }
           else {
               scale_step = 1000;
           }

           for (var i = 0; i < max/scale_step; i++)  {
               ctx.fillStyle = (i % 2) ? '#eeeeee' : '#e0e0e0';
               ctx.fillRect(0,i * scale_step, w, scale_step);
               ctx.save();
               ctx.translate(0, i*scale_step);
               ctx.scale(data.length/w, -1 * max / h);
               ctx.font = '12px serif';
               ctx.fillStyle = '#000000';
               ctx.fillText(format(i*scale_step), 0, 5);
               ctx.restore();
 
           }


           var plotval = 0;
           for (var i = 0; i < data.length; i++) {
               var january = false;
               if (data[i]['date'].substr(-2) == "01") {
                   ctx.fillStyle = '#ff0000';
                   january = true;
               }
               else {
                   ctx.fillStyle = '#000000';
               }
               if (cumul) {
                   plotval += parseInt(data[i]['count']);
               }
               else {
                   plotval = parseInt(data[i]['count']);
               }
               if (i == data.length - 1) {
                   // last month: plot the extrapolated amount in a different colour
                   ctx.fillRect(i, 0, 0.5, plotval - ext);    
                   ctx.fillStyle = '#ffaaaa';
                   ctx.fillRect(i, plotval - ext, 0.5, ext)
               }
               else {
                   ctx.fillRect(i, 0, 0.5, plotval);    
               }
               if(january) {
                   ctx.save();
                   ctx.translate(i, plotval);
                   ctx.scale(data.length/w, -1 * max / h);
                   ctx.rotate(270*Math.PI/180);
                   ctx.font = '12px serif';
                   ctx.fillText(data[i]['date'].substr(0,7), 10,6);
                   ctx.restore();
               }
           }
           ctx.restore();

           sum = format(sum);
           mon_max = format(mon_max);

           var stats = "<b>Total database entries:</b> " + sum + "<br><b>Highest number per month:</b> " + mon_max + " ("+mon_max_date+")<br><br>";
           document.getElementById(item + "_stat").innerHTML = stats;


       }
   }
   request.send(null);
}

function format (x) {
           if (x >= 1000000) {
               x = x + "";
               x = x.substr(0, 1) + "." + x.substr(1,3) + "." + x.substr(4,3);
           }
           else if (x >= 1000) {
               x = x + "";
               x = x.substr(0, x.length - 3) + "." + x.substr(-3, 3);
           }
           return x;
}

plot('signups',false);
plot('koch',false);
plot('groups',false);
plot('plaintext',false);
plot('words',false);
plot('callsigns',false);
plot('qtc',false);
</script>

