/**
 * map buttons action: remove, edit etc 
 * 
 * @param INT map_id
 * @param STRING what
 * @returns 
 */
function doMap(map_id, what) {
    var action_file;
    if (what=='delete')	{
        action_file = 'index.php?p=maps';
    }
    else  {
        action_file = 'index.php?p=map_edit';
    }
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', action_file);
    form.style.display = 'hidden';
    var i = document.createElement("input");
    i.type = "hidden";
    i.name = "map_id";
    i.value = map_id;
    form.appendChild(i);
    document.body.appendChild(form)

    if (what=='delete')	{
        var r = confirm("You are going to remove this map.\nAre you sure ?");
        if (r == true) {
            form.submit();
        } 	
    }
    else  {
        form.submit();
    }
} 

/**
 * user buttons action: remove, make admin etc 
 * 
 * @param INT user_id
 * @param STRING what
 * @returns 
 */
function doUser(user_id, what) {
    var action_file;
    action_file = 'index.php?p=users';
    
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', action_file);
    form.style.display = 'hidden';
    
    var i = document.createElement("input");
    i.type = "hidden";
    i.name = "user_id";
    i.value = user_id;
    form.appendChild(i);
    document.body.appendChild(form)
    
    var j = document.createElement("input");
    j.type = "hidden";
    j.name = "what";
    j.value = what;
    form.appendChild(j);
    document.body.appendChild(form)

    if (what=='delete')	{
        var r = confirm("You are going to remove this user.\nAre you sure ?");
        if (r == true) {
            form.submit();
        } 	
    }
    else if (what=='make_admin') {
        form.submit();
    }
}
