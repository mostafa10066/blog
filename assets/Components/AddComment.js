import React,{useState,useEffect} from 'react';

function AddComment({handleSubmitComment}) {
    const [errors,setErrors]=useState([])
    const handleSubmit=async(e)=>{
        e.preventDefault();
        const form = e.target;
        const numberOfErrors=await validation(form)
        if(numberOfErrors===0){
            handleSubmitComment(form.elements)
            resetForm(form)
        }

    }
    const validation=async (form)=>{
        setErrors([])
        let numErrors=0
        Array.from(form.elements).forEach(eachFormEl => {
            if(eachFormEl.value.trim()==="" && eachFormEl.name!=="submit") {
                numErrors++
                setErrors(prevState => [...prevState,{id:eachFormEl.name,value:eachFormEl.name+" is required"}])

            }
        })
        return numErrors
    }
    const resetForm=(form)=>{
        Array.from(form.elements).forEach(eachFormEl => {
            eachFormEl.value= ""
        })
    }
    return (
       <div>
           {
               errors.length>0 &&
               errors.map(error=>{
                   return <div style={{color:"red"}} key={error.id}>{error.value}</div>
               })
           }
           <form onSubmit={(e)=>handleSubmit(e)} method="post">
               <label htmlFor="">Comment</label>
               <textarea name="comment" id="" cols="30" rows="10">
            </textarea>
               <label htmlFor="">Email</label>
               <input type="email" name="email"/>
               <label htmlFor="">Name</label>
               <input type="text" name="name"/>
               <button name="submit" type="submit">Post comment</button>
           </form>
       </div>
    );
}

export default AddComment;