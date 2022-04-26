'use strict';
{
  const getDate = document.querySelector("[data-type='date']");
  const getPrice = document.querySelector("[data-type='price']");
  const getMemo = document.querySelector("[data-type='memo']");
  const date = document.getElementById('date');
  const price = document.getElementById('price');
  const memo = document.getElementById('memo');
  const errorDate = document.getElementById('error_date');
  const errorPrice = document.getElementById('error_price');

  const staticBackdrop = document.getElementById('staticBackdrop');
  if (staticBackdrop) {
    staticBackdrop.addEventListener('show.bs.modal', (e) => {
      errorDate.classList.add('hide');
      errorPrice.classList.add('hide');

      if (getDate.value === '') {
        e.preventDefault();
        errorDate.classList.remove('hide');
      }
      if (getPrice.value === '') {
        e.preventDefault();
        errorPrice.classList.remove('hide');
      }

      date.textContent = getDate.value;
      price.textContent = Number(getPrice.value).toLocaleString();
      memo.textContent = getMemo.value;
    });

    // Enterキーが押された時にSubmitされるのを抑制する
    document.getElementById("add_form").onkeypress = (e) => {
      // form1に入力されたキーを取得
      const key = e.keyCode || e.charCode || 0;
      // 13はEnterキーのキーコード
      if (key == 13) {
        // アクションを行わない
        e.preventDefault();
      }
    }
  }

  const deletes = document.querySelectorAll('.delete');
  deletes.forEach(span => {
    span.addEventListener('click', () => {
      if (!confirm('本当に削除しますか？')) {
        return;
      }
      span.parentNode.submit();
    })
  });
}
