/**
 * @file
 * Javascript for Field Example.
 */

// Import not needed because React & ReactDOM are loaded by *.libraries.yml
// import React from 'react';
// import ReactDOM from 'react-dom';

class Toggle extends React.Component {
    constructor(props) {
        super(props);
        this.state = {isToggleOn: true};

        // This binding is necessary to make `this` work in the callback
        this.handleEvent = this.handleEvent.bind(this);
    }

    handleEvent() {
        this.setState(state => ({
            isToggleOn: !state.isToggleOn
        }));
    }

    render() {
        return (
            <div>
                <a onMouseEnter={this.handleEvent} onMouseOut={this.handleEvent}>
                    {jQuery('#formula').attr('formula')}
                </a>
                <span>{this.state.isToggleOn ? '' : ' = '+jQuery('#formula').attr('compute')}</span>
            </div>
        );
    }
}

ReactDOM.render(
    <Toggle />,
    document.getElementById('react-app')
);