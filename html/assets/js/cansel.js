// キャンセルボタンを作る
document.getElementById('.use-menu-cupon').forEach(button => {
  button.addEventListener('click', function() {
    if (this.classList.contains('active')) {
      this.classList.remove('active');
      this.textContent = "このクーポン使う";
    } else {
      document.querySelectorAll('.use-menu-cupon').forEach(btn => {
        btn.classList.remove('active');
        btn.textContent = "このクーポンを使う";
      });
      this.classList.add('active');
      this.textContent = "選択中";
    }
  });
});