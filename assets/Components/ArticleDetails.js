import React,{useEffect,useState} from 'react';
import Article from "./Article";
import {useParams,useHistory} from "react-router-dom"
import axios from "axios";
import AddComment from "./AddComment";
import Comment from "./Comment";
function ArticleDetails() {
    const history = useHistory();
    const urlParam=useParams()
    const [article,setArticle]=useState(null)
    const [comments,setComments]=useState([])
    useEffect(()=>{
        axios({
            method:"get",
            url : `/api/v1/articles/${urlParam.slug}`
        }).then(result=>{
            setArticle(result.data.data)
        }).catch(err=>{

        })

        axios({
            method:"get",
            url : `/api/v1/articles/${urlParam.slug}/comments`
        }).then(result=>{
            setComments(result.data.data)
        }).catch(err=>{

        })
    },[])
    const handleSubmitComment=(formElements)=>{
        handleAddCommentToDb({text:formElements["comment"].value,name:formElements["name"].value,email:formElements["email"].value})
        .then(result=>{
            setComments(prevState => [result.data.data,...prevState])
            setArticle({...article,num_comments:article.num_comments+1})
        }).catch(err=>{

        })
    }
    function handleAddCommentToDb(data){
     return    axios({
            method:"post",
            url:`/api/v1/articles/${urlParam.slug}/comments`,
            data:{
                data:data
            }
        })
    }
    return (
        <div>
            <button onClick={history.goBack}>Back</button>
            {
                article &&
                    <div>
                        <Article article={article} details={true} />
                        <div>
                            <h3>comments</h3>
                            {
                                comments.map(comment=>{
                                    return <Comment key={comment.id} comment={comment}/>
                                })
                            }
                        </div>
                        <AddComment handleSubmitComment={handleSubmitComment}/>
                    </div>

            }

        </div>
    );
}

export default ArticleDetails;