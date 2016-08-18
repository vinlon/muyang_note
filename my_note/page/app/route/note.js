// 
// You can configure ngRoute as always, but to take advantage of SharedState location
// feature (i.e. close sidebar on backbutton) you should setup 'reloadOnSearch: false' 
// in order to avoid unwanted routing.
// 
app.config(function($routeProvider) {
  $routeProvider.when('/',              {templateUrl: 'app/view/home.html', reloadOnSearch: false});
  $routeProvider.when('/note',          {templateUrl: 'app/view/note.html', reloadOnSearch: false}); 
  $routeProvider.when('/carousel',      {templateUrl: 'app/view/carousel.html', reloadOnSearch: false});
  
  $routeProvider.when('/scroll',        {templateUrl: 'app/view/scroll.html', reloadOnSearch: false}); 
  $routeProvider.when('/toggle',        {templateUrl: 'app/view/toggle.html', reloadOnSearch: false}); 
  $routeProvider.when('/accordion',     {templateUrl: 'app/view/accordion.html', reloadOnSearch: false}); 
  $routeProvider.when('/overlay',       {templateUrl: 'app/view/overlay.html', reloadOnSearch: false}); 
  $routeProvider.when('/forms',         {templateUrl: 'app/view/forms.html', reloadOnSearch: false});
  $routeProvider.when('/dropdown',      {templateUrl: 'app/view/dropdown.html', reloadOnSearch: false});
  $routeProvider.when('/touch',         {templateUrl: 'app/view/touch.html', reloadOnSearch: false});
  $routeProvider.when('/swipe',         {templateUrl: 'app/view/swipe.html', reloadOnSearch: false});
  $routeProvider.when('/drag',          {templateUrl: 'app/view/drag.html', reloadOnSearch: false});
  $routeProvider.when('/drag2',         {templateUrl: 'app/view/drag2.html', reloadOnSearch: false});
});