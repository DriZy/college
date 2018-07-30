import $ from 'jquery';

class MyNotes{

    constructor(){
        this.events();
    }

    events(){
        $(".delete-note").on("click", this.deleteNote);
    }


    //Methods will go here
    deleteNote(e){

        var thisNote = $(e.target).parents("li")

        $.ajax({
            beforesend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', collegeData.nonce);
            },
            url: collegeData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type:'DELETE',
            success: (response) => {
                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry');
                console.log(response);
            }
            
        });
    }
}

export default MyNotes;