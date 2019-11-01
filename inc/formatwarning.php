<script>
	var warned = 0;

	/* check for proper format of the entered text */
	function checkspaces () {

			if (warned == 1) {
					return true;
			}

			var text = document.getElementById('eform').input.value;
			var text2 = document.getElementById('eform').text.value;

			/* different word counts? */
			if (wc(text) == wc(text2)) {
					return true;
			}

			document.getElementById('formatwarning').style.display = 'block';

			warned = 1;

			return false;
	}


	function wc (s) {
			a = s.replace(/\s+$/g, '');
			a = a.replace(/\s+/g, ' ');
			return a.split(' ').length;
	}

	function maxwl (s) {
			var maxwordlen = 0;
			a = s.replace(/\s/g, ' ');
			a = a.split(' ');
			for (i=0; i < a.length; i++) {
					if (a[i].length > maxwordlen) {
						maxwordlen = a[i].length;
					}
			}
			return maxwordlen;
	}

</script>


