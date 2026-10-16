(function () {
  'use strict';

  // Confirm before destructive actions: <form data-confirm="Are you sure?">
  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute && e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });

  // Show the chosen photo's file name in the post composer
  document.querySelectorAll('.file-pick input[type=file]').forEach(function (input) {
    input.addEventListener('change', function () {
      var label = input.parentElement.querySelector('[data-file-name]');
      if (label) label.textContent = input.files[0] ? input.files[0].name.slice(0, 24) : 'Photo';
    });
  });

  // Client-side check: confirm password must match
  document.querySelectorAll('[data-match]').forEach(function (field) {
    var other = document.querySelector(field.getAttribute('data-match'));
    function check() { field.setCustomValidity(other && field.value !== other.value ? 'Passwords do not match.' : ''); }
    field.addEventListener('input', check);
    if (other) other.addEventListener('input', check);
  });

  // Auto-grow the post composer
  document.querySelectorAll('.composer textarea').forEach(function (t) {
    t.addEventListener('input', function () { t.style.height = 'auto'; t.style.height = Math.min(t.scrollHeight, 240) + 'px'; });
  });

  // Chat: send without reloading and poll for new messages
  var chat = document.getElementById('chat');
  if (!chat) return;
  var form = document.getElementById('chat-form');
  var last = parseInt(chat.getAttribute('data-last'), 10) || 0;
  var busy = false;

  function scrollDown() { chat.scrollTop = chat.scrollHeight; }
  function add(m) {
    if (m.id <= last) return;
    last = m.id;
    var empty = document.getElementById('chat-empty');
    if (empty) empty.remove();
    var box = document.createElement('div');
    box.className = 'msg ' + (m.mine ? 'mine' : 'theirs');
    var p = document.createElement('p'); p.textContent = m.body;
    var s = document.createElement('small'); s.textContent = m.time;
    box.appendChild(p); box.appendChild(s);
    chat.appendChild(box);
  }
  scrollDown();

  form.addEventListener('submit', function (e) {
    var input = form.elements.body;
    if (!input.value.trim()) { e.preventDefault(); return; }
    e.preventDefault();
    fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' } })
      .then(function (r) { return r.json(); })
      .then(function (d) { if (d.messages) { d.messages.forEach(add); input.value = ''; scrollDown(); } })
      .catch(function () { form.submit(); });
  });

  setInterval(function () {
    if (document.hidden || busy) return;
    busy = true;
    fetch(chat.getAttribute('data-poll') + '&after=' + last, { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        var atBottom = chat.scrollHeight - chat.scrollTop - chat.clientHeight < 80;
        (d.messages || []).forEach(add);
        if (atBottom) scrollDown();
      })
      .catch(function () {})
      .then(function () { busy = false; });
  }, 4000);
})();
