<?
require_once './core/LoadMore.php';

class Profile {
    private $check_user = FALSE;

    function __construct($user_id) {
        $this -> user_id = $user_id;
        $this -> check_user = select("SELECT * FROM users WHERE id = $this->user_id")[0];
    }

    function display_title() {
        if (!$this->check_user) { // if incorrect user
            return $this->handle_incorrect_user();
        }

        return $this->handle_user_title($this->check_user);
    }

    private function handle_incorrect_user() {
        return "<h4>Похоже, вы ошиблись и такого пользователя нет</h4>";
    }

    private function handle_user_title($user) {
        $out = "<div class='profile__title-user'>
                    <img src='../images/user_icons/$user[id].jpeg'>&nbsp
                    <span>$user[login]</span>
                </div>".
                $this->user_edit();

        return $out;
    }

    private function user_edit() {
        $user = $_SESSION['user']['id'];

        if ($this->check_user['id'] === $user) return
        "<div class='profile__title-actions d-flex'>
            <span class='profile-edit-btn profile__title-edit' style='margin-right:5px;'>Редактировать</span>
        </div>";
    }

    function display_questions($profile_q) {
        if (!$this->check_user) return;

        $out = "<div class='profile__questions-count'>Вопросы, заданные <span style='font-weight:700;'>".$this->check_user['login']."</span>: $profile_q->count<hr></div>";
        $out .= $profile_q->display();

        return $out;
    }
}