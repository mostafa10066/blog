import React from 'react';
import Articles from "./Articles";
import ReactDOM from "react-dom";
import ArticleDetails from "./ArticleDetails";
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link
} from "react-router-dom";

function Main(props) {
    return (
        <div>
            <Router>
                <Switch>
                    <Route exact path="/">
                        <Articles />
                    </Route>
                    <Route exact path="/article/:slug">
                        <ArticleDetails />
                    </Route>
                </Switch>
            </Router>
        </div>
    );
}

import { createRoot } from 'react-dom/client';
const container = document.getElementById('root');
const root = createRoot(container); // createRoot(container!) if you use TypeScript
root.render(<Main />);
export default Main;