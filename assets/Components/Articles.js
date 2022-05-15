import React,{useEffect,useState} from 'react';
import axios from "axios";
import Article from "./Article";
import {Link} from "react-router-dom";

function Articles(props) {
    const[articles,setArticles]=useState([])
    useEffect(()=>{
        axios({
            method:"get",
            url:"/api/v1/articles"
        }).then(result=>{
            setArticles(result.data.data.items)
        }).catch(err=>{

        })
    },[])
    return (
        <div>
            {
                articles.map(article=>{
                    return <div key={article.id}>
                                <Article details={false} article={article}/>
                                <Link to={`/article/${article.slug}`}>Read more</Link>
                           </div>
                })
            }
        </div>

    );
}

export default Articles;