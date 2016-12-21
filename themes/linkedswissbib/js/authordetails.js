$(document).ready(function() {

    var uri2name = getURIs();
    replaceInAuthorUris(',',';');
    getAndSetNames();

    function getURIs() {

        var uri2name = {};

        $( ".authoruris" ).each(function() {

            var authoruris = this.innerHTML;

            authoruris = authoruris.replace(/\s+/g, ''); // remove spaces: http://stackoverflow.com/a/5963202
            authoruris = authoruris.split(','); // split entries when meeting a ',' and convert to array of strings: http://stackoverflow.com/a/5269873

            authoruris.forEach( function(authoruri) {
                uri2name[authoruri] = '';
            });
        });

        return uri2name;
    }


    function getAndSetNames() {

        for (var uri in uri2name) { // http://stackoverflow.com/a/921808

            // skip loop if the property is from prototype
            if (!uri2name.hasOwnProperty(uri)) continue;

            var uri = JSON.parse(JSON.stringify(uri)); // deep-copy uri

            var value = uri2name[uri];

            $.ajax({
                url: "http://" + window.location.hostname +
                "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": uri},
                success: function (msg) {
                    // Zugriff auf JSON über "msg"

                    if (msg.hasOwnProperty('organisation')) {
                        var uri = msg.organisation[0]._source["@id"]; // can't use variable 'uri' from outside directly
                        var name = extractName(msg.organisation[0]);

                    } else {
                        var uri = msg.person[0]._source["@id"]; // can't use variable 'uri' from outside directly
                        var name = extractName(msg.person[0]);
                    }
                    uri2name[uri] = name;

                    var fullNameString;
                    if (isAuthorUriRealPerson(uri)) {
                        fullNameString = '<a href="http://' + window.location.hostname +
                            '/sbrd/Exploration/AuthorDetails?lookfor=' + uri + '&type=AuthorForId">' + name + '</a>';
                        fullNameString += '<span class="fa fa-info-circle fa-lg kcopenerAuthor" authorId="' + uri +'"></span>';
                    } else {
                        fullNameString = name;
                    }
                    replaceInAuthorUris(uri, fullNameString);
                },
                error: function (e) {
                    console.log(e);
                }
            });
        }
    }

    function replaceInAuthorUris(theOld, theNew) {

        $( ".authoruris" ).each(function() {
            this.innerHTML = this.innerHTML.replace(new RegExp($.ui.autocomplete.escapeRegex(theOld), 'g'), theNew); // http://stackoverflow.com/a/13157996
        });
    }

    function isAuthorUriRealPerson(uri) {
        var substring = "http://data.swissbib.ch/organisation/";
        return !(isStringContainingSubstring(uri, substring));
    }

    function isStringContainingSubstring(string, substring) {
        return (string.indexOf(substring) !== -1); // http://stackoverflow.com/a/1789952
    }

    function checkForArrays (property) {
        if ($.isArray(property)) {
            return property[0];
        } else {
            return property;
        }
    }

    function extractName(json) {
        var source = json._source;

        if (('foaf:lastName' in source) && ('foaf:firstName') in source) {
            //not ideal solution since it matches first and last name that do not actually belong together
            var firstName = checkForArrays(source['foaf:firstName']);
            var lastName = checkForArrays(source['foaf:lastName']);
            return firstName + ' ' + lastName;
        } else if ('foaf:lastName' in source) {
            return checkForArrays(source['foaf:lastName']);
        } else if ('foaf:name' in source) {
            return source['foaf:name'][0];
        } else if ('rdf:type' != "http://xmlns.com/foaf/0.1/Person") {
            return checkForArrays(source['rdfs:label']);
        } else {
            return '';
        }
    }

});
