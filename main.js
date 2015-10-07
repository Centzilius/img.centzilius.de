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
		console.log(data);
		while (counter != maximg && counter <= imgmax) {
			var image = data[counter];
			console.log(image);
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
	var pg_li_back = document.createElement("li");
	var pg_li_back_a = document.createElement("a");
	pg_li_back_a.setAttribute("aria-label", "Previous");
	pg_li_back_a.setAttribute("onClick", "gotopage(" + (currentpage - 1) + ");");
	pg_li_back_a.setAttribute("id", "pg_li_back_a");
	var pg_li_next = document.createElement("li");
	var pg_li_next_a = document.createElement("a");
	pg_li_next_a.setAttribute("aira-label", "Next");
	pg_li_next_a.setAttribute("onClick", "gotopage(" + (currentpage + 1) + ");");
	pg_li_next_a.setAttribute("id", "pg_li_next_a");
	pg_li_back.appendChild(pg_li_back_a);
	pg_ul.appendChild(pg_li_back);
	pg_li_next.appendChild(pg_li_next_a);
	pg_ul.appendChild(pg_li_next);
	pg_nav.appendChild(pg_ul);
	document.getElementById("img-container").appendChild(pg_nav);
	$("#pg_li_next_a").html("&raquo;");
	$("#pg_li_back_a").html("&laquo;");
}

function gotopage(page) {
	counter = imgperpage * (page - 1)
	maximg = imgperpage * page
	buildImageContainer()
	pageination();
}
