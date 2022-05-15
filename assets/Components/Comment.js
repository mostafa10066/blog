import React from 'react';

function Comment({comment}) {
    return (
        <div className="comment">
            <h3>{comment.name}</h3>
            <h3>{comment.email}</h3>
            <div>{comment.text}</div>
        </div>
    );
}

export default Comment;