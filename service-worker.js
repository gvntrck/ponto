self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open('pontoapp-cache').then(function(cache) {
      return cache.addAll([
        '/',
        '/index.php',
        
        
        
        // adicione outros arquivos que você quer que sejam armazenados em cache
      ]);
    })
  );
});

self.addEventListener('fetch', function(e) {
  e.respondWith(
    caches.match(e.request).then(function(response) {
      return response || fetch(e.request);
    })
  );
});
 //versao:1
 