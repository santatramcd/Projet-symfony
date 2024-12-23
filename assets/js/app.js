import '../css/app.scss';
import { Dropdown } from 'bootstrap';

document.addEventListener('DOMContentLoaded',() =>{
    new App();
});

class App{
    constructor(){
        this.enableDropdowns();
        this.handleCommentForm();
    }
    enableDropdowns(){
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        dropdownElementList.map(function(dropdownToggleEl){
            return new Dropdown(dropdownToggleEl)
        });
    }

    handleCommentForm() {
        const commentForm = document.querySelector('form.comment-form');
    
        if (null === commentForm) {
            return;
        }
    
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
    
            const response = await fetch('/ajax/comments', {
                method: 'POST',
                body: new FormData(e.target)
                // body: new FormData(commentForm)
            });
    
            if (!response.ok) {
                console.error('Erreur lors de l\'ajout du commentaire.');
                return;
            }
    
            const json = await response.json();
    
            if (json.code === 'COMMENT_ADDED_SUCCESSFULLY') {
                const commentList = document.querySelector('.comment-list');
                const commentCount = document.querySelector('.comment-count');
                const commentContent = document.querySelector('#comment_content');
                // Ajoute le commentaire au DOM
                commentList.insertAdjacentHTML('beforeend', json.message);
                commentList.lastElementChild.scrollIntoView();
                commentCount.innerText = json.numberOfComments;
                commentContent.value='';
            } else {
                console.error('Erreur: ' + json.code);
            }
        });
    }
    
    
}
