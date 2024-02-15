function CurrentItemEditButton(props) {
    return <a className="btn btn-primary fa fa-edit" target="_blank"
              href={'admin/content/adminitem/edit/' + props.data.itemid}></a>;
}

const container = document.getElementById('react-edit-button');
const root = ReactDOM.createRoot(container);
root.render(<CurrentItemEditButton data={container.dataset}/>);