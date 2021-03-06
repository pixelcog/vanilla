jQuery(document).ready(function($) {
   
   // Load news & tutorials from Vanilla
   var lists = $('div.Column div.List'),
      newsColumn = $('div.NewsColumn div.List'),
      helpColumn = $('div.HelpColumn div.List');

   loadFeed = function(container, type, rows, format) {
      $.ajax({
         type: "GET",
         url: gdn.url('/dashboard/utility/getfeed/'+type+'/'+rows+'/'+format+'/'),
         success: function(data) {
            container.removeClass('Loading');
            container.html(data);
         },
         error: function() {
            container.removeClass('Loading');
            container.text('Failed to load '+type+' feed.');
         }
      });
   };

   lists.addClass('Loading');
   loadFeed(newsColumn, 'news', 3, 'extended');
   loadFeed(helpColumn, 'help', 3, 'extended');   
});