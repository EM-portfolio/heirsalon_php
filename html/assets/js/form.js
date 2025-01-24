
// form 生年月日入力制御
document.querySelector('input[name="barthday"]').addEventListener('input', function(e) {
  // 数値以外を除去
  let value = e.target.value.replace(/\D/g, ''); 
  if (value.length >= 4) value = value.slice(0, 4) + '/' + value.slice(4); 
  if (value.length >= 7) value = value.slice(0, 7) + '/' + value.slice(7); 
  e.target.value = value;
});
// 電話番号制御
document.querySelector('input[name="tel"]').addEventListener('input', function(e) {
  // 数値以外を除去
  let value = e.target.value.replace(/\D/g, ''); 
  if (value.length >= 3) value = value.slice(0, 3) + '-' + value.slice(3); 
  if (value.length >= 8) value = value.slice(0, 8) + '-' + value.slice(8); 
  e.target.value = value;
});
// form 郵便番号制御
document.querySelector('input[name="postal-code"]').addEventListener('input', function(e){
  let value = e.target.value.replace(/\D/g, '');
  if (value.length >= 4) value = value.slice(0, 3) + '-' + value.slice(3);
  e.target.value = value;
});

// ページ離脱の警告
// const beforeUnloaded = e => {
//   e.preventDefault();
//   e.returnValue = '';
// }

// window.addEventListener('beforeunload', beforeUnloaded);