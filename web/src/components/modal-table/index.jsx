import React ,{useState,useEffect}from "react";
import  {Modal} from  'antd';
import  {http} from  'src/library/ajax'
import CompGaiaTable from "src/components/gaia-table";

export  default  ({visible,url,columns,params,title,onCancel,width})=>{
	const [loading,setLoading]= useState(false)
	const [tableData,setTableData]=  useState([]);

	const  fetch=async()=>{
		setLoading(true)
	const {data}= await http.get(url,params)
		setTableData(data)
		setLoading(false)
	}

	useEffect(()=>{
		if(visible){
			fetch()
		}
	},[
		visible,params
	])

	return <Modal title={title} visible={visible} footer={null} width={width} onCancel={onCancel}>
		<CompGaiaTable
			columns={columns}
			loading={loading}
			dataSource={tableData}
		/>
	</Modal>
}