$(document).ready(function() {

    // Search autocomplete
    $('.twitterautocomplete').typeahead(
        {
            highlight: true,
            minLength: 3
        }, {
            templates: {
                header: '<h4 class="autocomplete-header">B&uuml;cher &amp; Co.</h4>'
            },
            displayKey: function(data) {
                return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
                //todo we have to throw away this stupid /sbrd and the global path variable in
                //layout.phtml line 25 (theme linked swissbib
                $.ajax({

                    url: '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:'Solr',
                        type:'Title'
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i< 5;i++) {
                                if (json.data[i][1] == 'BibRes') {
                                    datums.push({val: json.data[i]});
                                }
                            }
                            cb(datums);
                        } else {
                            cb([]);
                        }
                    }
                });
            }
        } , {
            templates: {
                header: '<h4 class="autocomplete-header">AutorInnen</h4>'
            },
            displayKey: function(data){
                    return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
                $.ajax({
                    url: path + '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:searcher['searcher'],
                        type:$('#searchForm_type').val()
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i < json.data.length;i++) {
                                if (json.data[i][1] == 'person') {
                                    if (datums.length < 5 ) {
                                        datums.push({val: json.data[i]});
                                    }
                                }
                            }
                            cb(datums);
                        } else {
                            cb([]);
                        }
                    }
                });
            }
        }, {
            templates: {
                header: '<h4 class="autocomplete-header">Themen</h4>'
            },
            displayKey: function(data) {
                return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
                $.ajax({
                    url: path + '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:searcher['searcher'],
                        type:$('#searchForm_type').val()
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i< json.data.length ;i++) {
                                if (json.data[i][1] == 'DEFAULT') {
                                    if (datums.length < 5 ) {
                                        datums.push({val: json.data[i]});
                                    }
                                }
                            }
                            cb(datums);
                        } else {
                            cb([]);
                        }
                    }
                });
            }
        } 
    ).bind('typeahead:selected', function(obj, datum, name) {

            //var baseurl = 'http://' + window.location.hostname + '/sbrd/';
            var baseurl = 'http://' + window.location.hostname;

            var postfix;

            if (datum['val'][1] == 'person') {
                postfix = 'Exploration/AuthorDetails?lookfor=' + datum['val'][0] + '&type=AuthorForId';
            } else if (datum['val'][1] == 'DEFAULT') {
                postfix = 'Exploration/SubjectDetails?lookfor=' + datum['val'][0] + '&type=SubjectById';
            } else if (datum['val'][1] == 'BibRes') {
                postfix = 'Record/' + datum['val'][0];
            }

            window.location.href = baseurl + "/" + postfix;
        });
});
