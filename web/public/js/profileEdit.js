getSession.then(d => {
    let editBtn = document.querySelector('.profile__title-edit');
    let form;
    let user = d.user;
    editBtn.onclick = handleEdit;

    function handleEdit() {
        let editForm = `
        <form class='profile-edit'>
        <label>Логин</label>
        <input type="text" name="login" placeholder="Новый логин"required value='${user.login}'>
        <label>Почта</label>
        <input type="email" name="email" placeholder="Введите новый адрес своей почты"required value='${user.email}'>
        <a href='/profile/change_icon'>Поменять изображение профиля</a>
        <button type="submit" class="profile-edit-submit" >Обновить</button>
        </form>`
        displayAlertModal('myModal', editForm); 
        
        //add event
        let submitBtn = document.querySelector('.profile-edit-submit');
        form = document.querySelector('.profile-edit');
    submitBtn.onclick = updateUserData;
    }

    function updateUserData(e) {
        e.preventDefault();

        let updated_data = {
            "login": form.querySelector('[name="login"]').value,
            "email": form.querySelector('[name="email"]').value
        };

        sendJSON('../ajax/update_user.php', updated_data)
        .then(() => location.reload())
    }
})