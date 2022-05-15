import React from 'react';
import moment from "moment";
import {Link} from "react-router-dom";
function Article({article,details}) {
    return (
        <div className="each-article">
            {
                details ?
                    <img src={article.teaser_image.cpath} alt=""/>
                :   <Link to={`/article/${article.slug}`}>
                        <img src={article.teaser_image.cpath} alt=""/>
                    </Link>
            }
            {
                details ?
                    <h1>{article.header}</h1>
                :   <Link to={`/article/${article.slug}`}>
                        <h1>{article.header}</h1>
                    </Link>
            }
            <div>
                <div>{moment(article.created_at).format("yyyy-MM-DD")}</div>
                <div>{article.writer.name+ ' '+article.writer.surname}</div>
                <div>{article.num_comments} <span>Comment</span></div>
            </div>
            <div dangerouslySetInnerHTML={{__html: article.body}} />
        </div>
    );
}

export default Article;