counter = 0;
imgperpage = 5;
maximg = imgperpage;
$.getJSON("api.php?action=getImages", function(res) {
	data = res;
	maxpage = data.length / imgperpage;
	imgmax = data.length - 1;
	buildImageContainer();
	pageination();
});
function buildImageContainer() {
		currentpage = (counter / imgperpage) + 1;
		$('#img-container').html("");
		var table = document.createElement('table');
		var thead = document.createElement('thead');
		var thr = document.createElement("tr");
		var imgth = document.createElement("th");
		imgth.setAttribute("width", "50%");
		var imgthtext = document.createTextNode("Vorschau");
		var linkth = document.createElement("th");
		var linkthtext = document.createTextNode("Dateiname");
		imgth.appendChild(imgthtext);
		linkth.appendChild(linkthtext);
		thr.appendChild(imgth);
		thr.appendChild(linkth);
		thead.appendChild(thr);
		var tbody = document.createElement('tbody');
		table.setAttribute("class", "table table-bordered table-hover");
		while (counter != maximg && counter <= imgmax) {
			var image = data[counter];
			var tr = document.createElement('tr');
			tr.setAttribute("id", image['filename']);
			var imgtd = document.createElement('td');
			var linktd = document.createElement('td');
			var imga = document.createElement("a");
			imga.href =  image["path"];
			var img = document.createElement("img");
			img.setAttribute("src", image["thumbnail"]);
			imga.appendChild(img);
			imgtd.appendChild(imga);
			var link = document.createElement("a");
			link.href = image["path"];
			link.appendChild(document.createTextNode(image["filename"]));
			linktd.appendChild(link);
			tr.appendChild(imgtd);
			tr.appendChild(linktd);
			tbody.appendChild(tr);
			counter += 1;
		}
		table.appendChild(thead);
		table.appendChild(tbody);
		document.getElementById("img-container").appendChild(table);
}

function pageination() {
	var pg_nav = document.createElement("nav");
	var pg_ul = document.createElement("ul");
	pg_ul.setAttribute("class", "pagination");
	if (currentpage != 1) {
		// «
		var pg_li_back = document.createElement("li");
		var pg_li_back_a = document.createElement("a");
		pg_li_back_a.setAttribute("aria-label", "Previous");
		pg_li_back_a.setAttribute("href", "#" + (currentpage - 1));
		pg_li_back_a.setAttribute("onClick", "gotopage(" + (currentpage - 1) + ");");
		pg_li_back_a.setAttribute("id", "pg_li_back_a");
		pg_li_back.appendChild(pg_li_back_a);
		pg_ul.appendChild(pg_li_back);
		
		var pg_li_first = document.createElement("li");
		var pg_li_first_a = document.createElement("a");
		pg_li_first_a.setAttribute("aria-label", "First");
		pg_li_first_a.setAttribute("href", "#" + "1");
		pg_li_first_a.setAttribute("onClick", "gotopage('1');");
		pg_li_first_a.setAttribute("id", "pg_li_first_a");
		pg_li_first.appendChild(pg_li_first_a);
		pg_ul.appendChild(pg_li_first);
	}
	
	if ((currentpage - 2) > 1) {
		// currentpage - 2
		var pg_li_2back = document.createElement("li");
		var pg_li_2back_a = document.createElement("a");
		pg_li_2back_a.setAttribute("aria-label", (currentpage - 2));
		pg_li_2back_a.setAttribute("href", "#" + (currentpage - 2));
		pg_li_2back_a.setAttribute("onClick", "gotopage(" + (currentpage - 2) + ");");
		pg_li_2back_a.setAttribute("id", "pg_li_2back_a");
		pg_li_2back.appendChild(pg_li_2back_a);
		pg_ul.appendChild(pg_li_2back);
	}
	
	if ((currentpage - 1) > 1) {
		// currentpage - 1
		var pg_li_1back = document.createElement("li");
		var pg_li_1back_a = document.createElement("a");
		pg_li_1back_a.setAttribute("aria-label", (currentpage - 1));
		pg_li_1back_a.setAttribute("href", "#" + (currentpage - 1));
		pg_li_1back_a.setAttribute("onClick", "gotopage(" + (currentpage - 1) + ");");
		pg_li_1back_a.setAttribute("id", "pg_li_1back_a");
		pg_li_1back.appendChild(pg_li_1back_a);
		pg_ul.appendChild(pg_li_1back);
	}
	
	var pg_li_current = document.createElement("li");
	var pg_li_current_a = document.createElement("a");
	pg_li_current_a.setAttribute("aria-label", currentpage);
//	pg_li_current_a.setAttribute("href", "#" + currentpage);
//	pg_li_current_a.setAttribute("onClick", "gotopage(" + currentpage + ");");
	pg_li_current_a.setAttribute("id", "pg_li_current_a");
	pg_li_current.appendChild(pg_li_current_a);
	pg_ul.appendChild(pg_li_current);

	if ((currentpage < Math.ceil(maxpage)) && (currentpage + 1 != Math.ceil(maxpage))) {
		var pg_li_1next = document.createElement("li");
		var pg_li_1next_a = document.createElement("a");
		pg_li_1next_a.setAttribute("aira-label", currentpage + 1);
		pg_li_1next_a.setAttribute("href", "#" + (currentpage + 1));
		pg_li_1next_a.setAttribute("onClick", "gotopage(" + (currentpage + 1) + ");");
		pg_li_1next_a.setAttribute("id", "pg_li_1next_a");
		pg_li_1next.appendChild(pg_li_1next_a);
		pg_ul.appendChild(pg_li_1next);
	}
	
	if (((currentpage + 1) < Math.ceil(maxpage)) && (currentpage + 2 != Math.ceil(maxpage))) {
		var pg_li_2next = document.createElement("li");
		var pg_li_2next_a = document.createElement("a");
		pg_li_2next_a.setAttribute("aira-label", currentpage + 2);
		pg_li_2next_a.setAttribute("href", "#" + (currentpage + 2));
		pg_li_2next_a.setAttribute("onClick", "gotopage(" + (currentpage + 2) + ");");
		pg_li_2next_a.setAttribute("id", "pg_li_2next_a");
		pg_li_2next.appendChild(pg_li_2next_a);
		pg_ul.appendChild(pg_li_2next);
	}
	
	if (currentpage != Math.ceil(maxpage)) {
		var pg_li_last = document.createElement("li");
		var pg_li_last_a = document.createElement("a");
		pg_li_last_a.setAttribute("aira-label", "Next");
		pg_li_last_a.setAttribute("href", "#" + Math.ceil(maxpage));
		pg_li_last_a.setAttribute("onClick", "gotopage(" + Math.ceil(maxpage) + ");");
		pg_li_last_a.setAttribute("id", "pg_li_last_a");
		pg_li_last.appendChild(pg_li_last_a);
		pg_ul.appendChild(pg_li_last);
		
		var pg_li_next = document.createElement("li");
		var pg_li_next_a = document.createElement("a");
		pg_li_next_a.setAttribute("aira-label", "Next");
		pg_li_next_a.setAttribute("href", "#" + (currentpage + 1));
		pg_li_next_a.setAttribute("onClick", "gotopage(" + (currentpage + 1) + ");");
		pg_li_next_a.setAttribute("id", "pg_li_next_a");
		pg_li_next.appendChild(pg_li_next_a);
		pg_ul.appendChild(pg_li_next);
	}
	
	pg_nav.appendChild(pg_ul);
	document.getElementById("img-container").appendChild(pg_nav);
	$("#pg_li_next_a").html("&raquo;");
	$("#pg_li_back_a").html("&laquo;");
	$("#pg_li_1back_a").html(currentpage - 1);
	$("#pg_li_2back_a").html(currentpage - 2);
	$("#pg_li_1next_a").html(currentpage + 1);
	$("#pg_li_2next_a").html(currentpage + 2);
	$("#pg_li_current_a").html(currentpage);
	$("#pg_li_last_a").html(Math.ceil(maxpage));
	$("#pg_li_first_a").html("1");

}

function gotopage(page) {
	counter = imgperpage * (page - 1)
	maximg = imgperpage * page
	buildImageContainer()
	pageination();
}

if(window.location.hash) {
	var hash = window.location.hash.substring(1);
	gotopage(hash);
}
