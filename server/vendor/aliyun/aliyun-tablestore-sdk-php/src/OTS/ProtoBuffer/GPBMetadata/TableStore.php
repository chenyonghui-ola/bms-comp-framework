<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store.proto

namespace GPBMetadata;

class TableStore
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(hex2bin(
            "0af35b0a117461626c655f73746f72652e70726f746f121f616c6979756e" .
            "2e4f54532e50726f746f4275666665722e50726f746f636f6c22260a0545" .
            "72726f72120c0a04636f6465180120022809120f0a076d65737361676518" .
            "022001280922a2010a105072696d6172794b6579536368656d61120c0a04" .
            "6e616d65180120022809123d0a047479706518022002280e322f2e616c69" .
            "79756e2e4f54532e50726f746f4275666665722e50726f746f636f6c2e50" .
            "72696d6172794b65795479706512410a066f7074696f6e18032001280e32" .
            "312e616c6979756e2e4f54532e50726f746f4275666665722e50726f746f" .
            "636f6c2e5072696d6172794b65794f7074696f6e22650a13446566696e65" .
            "64436f6c756d6e536368656d61120c0a046e616d6518012002280912400a" .
            "047479706518022002280e32322e616c6979756e2e4f54532e50726f746f" .
            "4275666665722e50726f746f636f6c2e446566696e6564436f6c756d6e54" .
            "797065222c0a0e506172746974696f6e52616e6765120d0a05626567696e" .
            "18012002280c120b0a03656e6418022002280c22d8010a0c5461626c654f" .
            "7074696f6e7312140a0c74696d655f746f5f6c6976651801200128051214" .
            "0a0c6d61785f76657273696f6e73180220012805124b0a11626c6f6f6d5f" .
            "66696c7465725f7479706518032001280e32302e616c6979756e2e4f5453" .
            "2e50726f746f4275666665722e50726f746f636f6c2e426c6f6f6d46696c" .
            "7465725479706512120a0a626c6f636b5f73697a6518042001280512250a" .
            "1d646576696174696f6e5f63656c6c5f76657273696f6e5f696e5f736563" .
            "18052001280312140a0c616c6c6f775f75706461746518062001280822d3" .
            "010a09496e6465784d657461120c0a046e616d6518012002280912130a0b" .
            "7072696d6172795f6b657918022003280912160a0e646566696e65645f63" .
            "6f6c756d6e180320032809124b0a11696e6465785f7570646174655f6d6f" .
            "646518042002280e32302e616c6979756e2e4f54532e50726f746f427566" .
            "6665722e50726f746f636f6c2e496e6465785570646174654d6f6465123e" .
            "0a0a696e6465785f7479706518052002280e322a2e616c6979756e2e4f54" .
            "532e50726f746f4275666665722e50726f746f636f6c2e496e6465785479" .
            "706522f5010a095461626c654d65746112120a0a7461626c655f6e616d65" .
            "18012002280912460a0b7072696d6172795f6b657918022003280b32312e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e5072696d6172794b6579536368656d61124c0a0e646566696e65645f" .
            "636f6c756d6e18032003280b32342e616c6979756e2e4f54532e50726f74" .
            "6f4275666665722e50726f746f636f6c2e446566696e6564436f6c756d6e" .
            "536368656d61123e0a0a696e6465785f6d65746118042003280b322a2e61" .
            "6c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f6c" .
            "2e496e6465784d65746122760a09436f6e646974696f6e124f0a0d726f77" .
            "5f6578697374656e636518012002280e32382e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e526f77457869737465" .
            "6e63654578706563746174696f6e12180a10636f6c756d6e5f636f6e6469" .
            "74696f6e18022001280c222b0a0c4361706163697479556e6974120c0a04" .
            "72656164180120012805120d0a0577726974651802200128052299010a19" .
            "52657365727665645468726f75676870757444657461696c7312440a0d63" .
            "617061636974795f756e697418012002280b322d2e616c6979756e2e4f54" .
            "532e50726f746f4275666665722e50726f746f636f6c2e43617061636974" .
            "79556e6974121a0a126c6173745f696e6372656173655f74696d65180220" .
            "022803121a0a126c6173745f64656372656173655f74696d651803200128" .
            "03225a0a1252657365727665645468726f75676870757412440a0d636170" .
            "61636974795f756e697418012002280b322d2e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e436170616369747955" .
            "6e697422580a10436f6e73756d6564436170616369747912440a0d636170" .
            "61636974795f756e697418012002280b322d2e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e436170616369747955" .
            "6e697422450a1353747265616d53706563696669636174696f6e12150a0d" .
            "656e61626c655f73747265616d18012002280812170a0f65787069726174" .
            "696f6e5f74696d65180220012805226c0a0d53747265616d44657461696c" .
            "7312150a0d656e61626c655f73747265616d18012002280812110a097374" .
            "7265616d5f696418022001280912170a0f65787069726174696f6e5f7469" .
            "6d6518032001280512180a106c6173745f656e61626c655f74696d651804" .
            "2001280322bd030a124372656174655461626c6552657175657374123e0a" .
            "0a7461626c655f6d65746118012002280b322a2e616c6979756e2e4f5453" .
            "2e50726f746f4275666665722e50726f746f636f6c2e5461626c654d6574" .
            "6112500a1372657365727665645f7468726f75676870757418022002280b" .
            "32332e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e52657365727665645468726f75676870757412440a0d746162" .
            "6c655f6f7074696f6e7318032001280b322d2e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e5461626c654f707469" .
            "6f6e7312430a0a706172746974696f6e7318042003280b322f2e616c6979" .
            "756e2e4f54532e50726f746f4275666665722e50726f746f636f6c2e5061" .
            "72746974696f6e52616e676512490a0b73747265616d5f73706563180520" .
            "01280b32342e616c6979756e2e4f54532e50726f746f4275666665722e50" .
            "726f746f636f6c2e53747265616d53706563696669636174696f6e123f0a" .
            "0b696e6465785f6d6574617318072003280b322a2e616c6979756e2e4f54" .
            "532e50726f746f4275666665722e50726f746f636f6c2e496e6465784d65" .
            "746122150a134372656174655461626c65526573706f6e73652288010a12" .
            "437265617465496e6465785265717565737412170a0f6d61696e5f746162" .
            "6c655f6e616d65180120022809123e0a0a696e6465785f6d657461180220" .
            "02280b322a2e616c6979756e2e4f54532e50726f746f4275666665722e50" .
            "726f746f636f6c2e496e6465784d65746112190a11696e636c7564655f62" .
            "6173655f6461746118032001280822150a13437265617465496e64657852" .
            "6573706f6e7365223f0a1044726f70496e6465785265717565737412170a" .
            "0f6d61696e5f7461626c655f6e616d6518012002280912120a0a696e6465" .
            "785f6e616d6518022002280922130a1144726f70496e646578526573706f" .
            "6e7365228b020a125570646174655461626c655265717565737412120a0a" .
            "7461626c655f6e616d6518012002280912500a1372657365727665645f74" .
            "68726f75676870757418022001280b32332e616c6979756e2e4f54532e50" .
            "726f746f4275666665722e50726f746f636f6c2e52657365727665645468" .
            "726f75676870757412440a0d7461626c655f6f7074696f6e731803200128" .
            "0b322d2e616c6979756e2e4f54532e50726f746f4275666665722e50726f" .
            "746f636f6c2e5461626c654f7074696f6e7312490a0b73747265616d5f73" .
            "70656318042001280b32342e616c6979756e2e4f54532e50726f746f4275" .
            "666665722e50726f746f636f6c2e53747265616d53706563696669636174" .
            "696f6e2284020a135570646174655461626c65526573706f6e7365125f0a" .
            "1b72657365727665645f7468726f7567687075745f64657461696c731801" .
            "2002280b323a2e616c6979756e2e4f54532e50726f746f4275666665722e" .
            "50726f746f636f6c2e52657365727665645468726f756768707574446574" .
            "61696c7312440a0d7461626c655f6f7074696f6e7318022002280b322d2e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e5461626c654f7074696f6e7312460a0e73747265616d5f6465746169" .
            "6c7318032001280b322e2e616c6979756e2e4f54532e50726f746f427566" .
            "6665722e50726f746f636f6c2e53747265616d44657461696c73222a0a14" .
            "44657363726962655461626c655265717565737412120a0a7461626c655f" .
            "6e616d6518012002280922e1030a1544657363726962655461626c655265" .
            "73706f6e7365123e0a0a7461626c655f6d65746118012002280b322a2e61" .
            "6c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f6c" .
            "2e5461626c654d657461125f0a1b72657365727665645f7468726f756768" .
            "7075745f64657461696c7318022002280b323a2e616c6979756e2e4f5453" .
            "2e50726f746f4275666665722e50726f746f636f6c2e5265736572766564" .
            "5468726f75676870757444657461696c7312440a0d7461626c655f6f7074" .
            "696f6e7318032002280b322d2e616c6979756e2e4f54532e50726f746f42" .
            "75666665722e50726f746f636f6c2e5461626c654f7074696f6e7312420a" .
            "0c7461626c655f73746174757318042002280e322c2e616c6979756e2e4f" .
            "54532e50726f746f4275666665722e50726f746f636f6c2e5461626c6553" .
            "746174757312460a0e73747265616d5f64657461696c7318052001280b32" .
            "2e2e616c6979756e2e4f54532e50726f746f4275666665722e50726f746f" .
            "636f6c2e53747265616d44657461696c7312140a0c73686172645f73706c" .
            "69747318062003280c123f0a0b696e6465785f6d6574617318082003280b" .
            "322a2e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e496e6465784d65746122120a104c6973745461626c65526571" .
            "7565737422280a114c6973745461626c65526573706f6e736512130a0b74" .
            "61626c655f6e616d657318012003280922280a1244656c6574655461626c" .
            "655265717565737412120a0a7461626c655f6e616d651801200228092215" .
            "0a1344656c6574655461626c65526573706f6e736522260a104c6f616454" .
            "61626c655265717565737412120a0a7461626c655f6e616d651801200228" .
            "0922130a114c6f61645461626c65526573706f6e736522280a12556e6c6f" .
            "61645461626c655265717565737412120a0a7461626c655f6e616d651801" .
            "2002280922150a13556e6c6f61645461626c65526573706f6e736522480a" .
            "0954696d6552616e676512120a0a73746172745f74696d65180120012803" .
            "12100a08656e645f74696d6518022001280312150a0d7370656369666963" .
            "5f74696d65180320012803226e0a0d52657475726e436f6e74656e741240" .
            "0a0b72657475726e5f7479706518012001280e322b2e616c6979756e2e4f" .
            "54532e50726f746f4275666665722e50726f746f636f6c2e52657475726e" .
            "54797065121b0a1372657475726e5f636f6c756d6e5f6e616d6573180220" .
            "03280922a3020a0d476574526f775265717565737412120a0a7461626c65" .
            "5f6e616d6518012002280912130a0b7072696d6172795f6b657918022002" .
            "280c12160a0e636f6c756d6e735f746f5f676574180320032809123e0a0a" .
            "74696d655f72616e676518042001280b322a2e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e54696d6552616e6765" .
            "12140a0c6d61785f76657273696f6e73180520012805121a0a0c63616368" .
            "655f626c6f636b731806200128083a0474727565120e0a0666696c746572" .
            "18072001280c12140a0c73746172745f636f6c756d6e1808200128091212" .
            "0a0a656e645f636f6c756d6e180920012809120d0a05746f6b656e180a20" .
            "01280c12160a0e7472616e73616374696f6e5f6964180b2001280922760a" .
            "0e476574526f77526573706f6e736512430a08636f6e73756d6564180120" .
            "02280b32312e616c6979756e2e4f54532e50726f746f4275666665722e50" .
            "726f746f636f6c2e436f6e73756d65644361706163697479120b0a03726f" .
            "7718022002280c12120a0a6e6578745f746f6b656e18032001280c22d901" .
            "0a10557064617465526f775265717565737412120a0a7461626c655f6e61" .
            "6d6518012002280912120a0a726f775f6368616e676518022002280c123d" .
            "0a09636f6e646974696f6e18032002280b322a2e616c6979756e2e4f5453" .
            "2e50726f746f4275666665722e50726f746f636f6c2e436f6e646974696f" .
            "6e12460a0e72657475726e5f636f6e74656e7418042001280b322e2e616c" .
            "6979756e2e4f54532e50726f746f4275666665722e50726f746f636f6c2e" .
            "52657475726e436f6e74656e7412160a0e7472616e73616374696f6e5f69" .
            "6418052001280922650a11557064617465526f77526573706f6e73651243" .
            "0a08636f6e73756d656418012002280b32312e616c6979756e2e4f54532e" .
            "50726f746f4275666665722e50726f746f636f6c2e436f6e73756d656443" .
            "61706163697479120b0a03726f7718022001280c22cf010a0d507574526f" .
            "775265717565737412120a0a7461626c655f6e616d65180120022809120b" .
            "0a03726f7718022002280c123d0a09636f6e646974696f6e18032002280b" .
            "322a2e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e436f6e646974696f6e12460a0e72657475726e5f636f6e7465" .
            "6e7418042001280b322e2e616c6979756e2e4f54532e50726f746f427566" .
            "6665722e50726f746f636f6c2e52657475726e436f6e74656e7412160a0e" .
            "7472616e73616374696f6e5f696418052001280922620a0e507574526f77" .
            "526573706f6e736512430a08636f6e73756d656418012002280b32312e61" .
            "6c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f6c" .
            "2e436f6e73756d65644361706163697479120b0a03726f7718022001280c" .
            "22da010a1044656c657465526f775265717565737412120a0a7461626c65" .
            "5f6e616d6518012002280912130a0b7072696d6172795f6b657918022002" .
            "280c123d0a09636f6e646974696f6e18032002280b322a2e616c6979756e" .
            "2e4f54532e50726f746f4275666665722e50726f746f636f6c2e436f6e64" .
            "6974696f6e12460a0e72657475726e5f636f6e74656e7418042001280b32" .
            "2e2e616c6979756e2e4f54532e50726f746f4275666665722e50726f746f" .
            "636f6c2e52657475726e436f6e74656e7412160a0e7472616e7361637469" .
            "6f6e5f696418052001280922650a1144656c657465526f77526573706f6e" .
            "736512430a08636f6e73756d656418012002280b32312e616c6979756e2e" .
            "4f54532e50726f746f4275666665722e50726f746f636f6c2e436f6e7375" .
            "6d65644361706163697479120b0a03726f7718022001280c2297020a1954" .
            "61626c65496e4261746368476574526f775265717565737412120a0a7461" .
            "626c655f6e616d6518012002280912130a0b7072696d6172795f6b657918" .
            "022003280c120d0a05746f6b656e18032003280c12160a0e636f6c756d6e" .
            "735f746f5f676574180420032809123e0a0a74696d655f72616e67651805" .
            "2001280b322a2e616c6979756e2e4f54532e50726f746f4275666665722e" .
            "50726f746f636f6c2e54696d6552616e676512140a0c6d61785f76657273" .
            "696f6e73180620012805121a0a0c63616368655f626c6f636b7318072001" .
            "28083a0474727565120e0a0666696c74657218082001280c12140a0c7374" .
            "6172745f636f6c756d6e18092001280912120a0a656e645f636f6c756d6e" .
            "180a2001280922600a124261746368476574526f7752657175657374124a" .
            "0a067461626c657318012003280b323a2e616c6979756e2e4f54532e5072" .
            "6f746f4275666665722e50726f746f636f6c2e5461626c65496e42617463" .
            "68476574526f775265717565737422c6010a18526f77496e426174636847" .
            "6574526f77526573706f6e7365120d0a0569735f6f6b1801200228081235" .
            "0a056572726f7218022001280b32262e616c6979756e2e4f54532e50726f" .
            "746f4275666665722e50726f746f636f6c2e4572726f7212430a08636f6e" .
            "73756d656418032001280b32312e616c6979756e2e4f54532e50726f746f" .
            "4275666665722e50726f746f636f6c2e436f6e73756d6564436170616369" .
            "7479120b0a03726f7718042001280c12120a0a6e6578745f746f6b656e18" .
            "052001280c22790a1a5461626c65496e4261746368476574526f77526573" .
            "706f6e736512120a0a7461626c655f6e616d6518012002280912470a0472" .
            "6f777318022003280b32392e616c6979756e2e4f54532e50726f746f4275" .
            "666665722e50726f746f636f6c2e526f77496e4261746368476574526f77" .
            "526573706f6e736522620a134261746368476574526f77526573706f6e73" .
            "65124b0a067461626c657318012003280b323b2e616c6979756e2e4f5453" .
            "2e50726f746f4275666665722e50726f746f636f6c2e5461626c65496e42" .
            "61746368476574526f77526573706f6e736522f4010a19526f77496e4261" .
            "7463685772697465526f7752657175657374123c0a047479706518012002" .
            "280e322e2e616c6979756e2e4f54532e50726f746f4275666665722e5072" .
            "6f746f636f6c2e4f7065726174696f6e5479706512120a0a726f775f6368" .
            "616e676518022002280c123d0a09636f6e646974696f6e18032002280b32" .
            "2a2e616c6979756e2e4f54532e50726f746f4275666665722e50726f746f" .
            "636f6c2e436f6e646974696f6e12460a0e72657475726e5f636f6e74656e" .
            "7418042001280b322e2e616c6979756e2e4f54532e50726f746f42756666" .
            "65722e50726f746f636f6c2e52657475726e436f6e74656e74227b0a1b54" .
            "61626c65496e42617463685772697465526f775265717565737412120a0a" .
            "7461626c655f6e616d6518012002280912480a04726f777318022003280b" .
            "323a2e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e526f77496e42617463685772697465526f7752657175657374" .
            "227c0a1442617463685772697465526f7752657175657374124c0a067461" .
            "626c657318012003280b323c2e616c6979756e2e4f54532e50726f746f42" .
            "75666665722e50726f746f636f6c2e5461626c65496e4261746368577269" .
            "7465526f775265717565737412160a0e7472616e73616374696f6e5f6964" .
            "18022001280922b4010a1a526f77496e42617463685772697465526f7752" .
            "6573706f6e7365120d0a0569735f6f6b18012002280812350a056572726f" .
            "7218022001280b32262e616c6979756e2e4f54532e50726f746f42756666" .
            "65722e50726f746f636f6c2e4572726f7212430a08636f6e73756d656418" .
            "032001280b32312e616c6979756e2e4f54532e50726f746f427566666572" .
            "2e50726f746f636f6c2e436f6e73756d65644361706163697479120b0a03" .
            "726f7718042001280c227d0a1c5461626c65496e42617463685772697465" .
            "526f77526573706f6e736512120a0a7461626c655f6e616d651801200228" .
            "0912490a04726f777318022003280b323b2e616c6979756e2e4f54532e50" .
            "726f746f4275666665722e50726f746f636f6c2e526f77496e4261746368" .
            "5772697465526f77526573706f6e736522660a1542617463685772697465" .
            "526f77526573706f6e7365124d0a067461626c657318012003280b323d2e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e5461626c65496e42617463685772697465526f77526573706f6e7365" .
            "2285050a0f47657452616e67655265717565737412120a0a7461626c655f" .
            "6e616d65180120022809123d0a09646972656374696f6e18022002280e32" .
            "2a2e616c6979756e2e4f54532e50726f746f4275666665722e50726f746f" .
            "636f6c2e446972656374696f6e12160a0e636f6c756d6e735f746f5f6765" .
            "74180320032809123e0a0a74696d655f72616e676518042001280b322a2e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e54696d6552616e676512140a0c6d61785f76657273696f6e73180520" .
            "012805120d0a056c696d697418062001280512230a1b696e636c75736976" .
            "655f73746172745f7072696d6172795f6b657918072002280c12210a1965" .
            "78636c75736976655f656e645f7072696d6172795f6b657918082002280c" .
            "121a0a0c63616368655f626c6f636b731809200128083a0474727565120e" .
            "0a0666696c746572180a2001280c12140a0c73746172745f636f6c756d6e" .
            "180b2001280912120a0a656e645f636f6c756d6e180c20012809120d0a05" .
            "746f6b656e180d2001280c12160a0e7472616e73616374696f6e5f696418" .
            "0e20012809125e0a14646174615f626c6f636b5f747970655f68696e7418" .
            "0f2001280e322e2e616c6979756e2e4f54532e50726f746f427566666572" .
            "2e50726f746f636f6c2e44617461426c6f636b547970653a104442545f50" .
            "4c41494e5f42554646455212280a1a72657475726e5f656e746972655f70" .
            "72696d6172795f6b6579731810200128083a047472756512530a12636f6d" .
            "70726573735f747970655f68696e7418112001280e322d2e616c6979756e" .
            "2e4f54532e50726f746f4275666665722e50726f746f636f6c2e436f6d70" .
            "72657373547970653a084350545f4e4f4e4522a8020a1047657452616e67" .
            "65526573706f6e736512430a08636f6e73756d656418012002280b32312e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e436f6e73756d65644361706163697479120c0a04726f777318022002" .
            "280c121e0a166e6578745f73746172745f7072696d6172795f6b65791803" .
            "2001280c12120a0a6e6578745f746f6b656e18042001280c12470a0f6461" .
            "74615f626c6f636b5f7479706518052001280e322e2e616c6979756e2e4f" .
            "54532e50726f746f4275666665722e50726f746f636f6c2e44617461426c" .
            "6f636b5479706512440a0d636f6d70726573735f7479706518062001280e" .
            "322d2e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e436f6d707265737354797065223f0a1c53746172744c6f6361" .
            "6c5472616e73616374696f6e5265717565737412120a0a7461626c655f6e" .
            "616d65180120022809120b0a036b657918022002280c22370a1d53746172" .
            "744c6f63616c5472616e73616374696f6e526573706f6e736512160a0e74" .
            "72616e73616374696f6e5f696418012002280922320a18436f6d6d697454" .
            "72616e73616374696f6e5265717565737412160a0e7472616e7361637469" .
            "6f6e5f6964180120022809221b0a19436f6d6d69745472616e7361637469" .
            "6f6e526573706f6e736522310a1741626f72745472616e73616374696f6e" .
            "5265717565737412160a0e7472616e73616374696f6e5f69641801200228" .
            "09221a0a1841626f72745472616e73616374696f6e526573706f6e736522" .
            "270a114c69737453747265616d5265717565737412120a0a7461626c655f" .
            "6e616d6518012001280922460a0653747265616d12110a0973747265616d" .
            "5f696418012002280912120a0a7461626c655f6e616d6518022002280912" .
            "150a0d6372656174696f6e5f74696d65180320022803224e0a124c697374" .
            "53747265616d526573706f6e736512380a0773747265616d731801200328" .
            "0b32272e616c6979756e2e4f54532e50726f746f4275666665722e50726f" .
            "746f636f6c2e53747265616d224d0a0b53747265616d536861726412100a" .
            "0873686172645f696418012002280912110a09706172656e745f69641802" .
            "2001280912190a11706172656e745f7369626c696e675f69641803200128" .
            "0922610a15446573637269626553747265616d5265717565737412110a09" .
            "73747265616d5f696418012002280912200a18696e636c75736976655f73" .
            "746172745f73686172645f696418022001280912130a0b73686172645f6c" .
            "696d6974180320012805228a020a16446573637269626553747265616d52" .
            "6573706f6e736512110a0973747265616d5f696418012002280912170a0f" .
            "65787069726174696f6e5f74696d6518022002280512120a0a7461626c65" .
            "5f6e616d6518032002280912150a0d6372656174696f6e5f74696d651804" .
            "2002280312440a0d73747265616d5f73746174757318052002280e322d2e" .
            "616c6979756e2e4f54532e50726f746f4275666665722e50726f746f636f" .
            "6c2e53747265616d537461747573123c0a0673686172647318062003280b" .
            "322c2e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e53747265616d536861726412150a0d6e6578745f7368617264" .
            "5f6964180720012809223e0a1747657453686172644974657261746f7252" .
            "65717565737412110a0973747265616d5f696418012002280912100a0873" .
            "686172645f696418022002280922320a1847657453686172644974657261" .
            "746f72526573706f6e736512160a0e73686172645f6974657261746f7218" .
            "0120022809223f0a1647657453747265616d5265636f7264526571756573" .
            "7412160a0e73686172645f6974657261746f72180120022809120d0a056c" .
            "696d697418022001280522f7010a1747657453747265616d5265636f7264" .
            "526573706f6e7365125d0a0e73747265616d5f7265636f72647318012003" .
            "280b32452e616c6979756e2e4f54532e50726f746f4275666665722e5072" .
            "6f746f636f6c2e47657453747265616d5265636f7264526573706f6e7365" .
            "2e53747265616d5265636f7264121b0a136e6578745f73686172645f6974" .
            "657261746f721802200128091a600a0c53747265616d5265636f72641240" .
            "0a0b616374696f6e5f7479706518012002280e322b2e616c6979756e2e4f" .
            "54532e50726f746f4275666665722e50726f746f636f6c2e416374696f6e" .
            "54797065120e0a067265636f726418022002280c226a0a1f436f6d707574" .
            "6553706c6974506f696e7473427953697a655265717565737412120a0a74" .
            "61626c655f6e616d6518012002280912120a0a73706c69745f73697a6518" .
            "0220022803121f0a1773706c69745f73697a655f756e69745f696e5f6279" .
            "746518032001280322d7020a20436f6d7075746553706c6974506f696e74" .
            "73427953697a65526573706f6e736512430a08636f6e73756d6564180120" .
            "02280b32312e616c6979756e2e4f54532e50726f746f4275666665722e50" .
            "726f746f636f6c2e436f6e73756d6564436170616369747912410a067363" .
            "68656d6118022003280b32312e616c6979756e2e4f54532e50726f746f42" .
            "75666665722e50726f746f636f6c2e5072696d6172794b6579536368656d" .
            "6112140a0c73706c69745f706f696e747318032003280c12620a096c6f63" .
            "6174696f6e7318042003280b324f2e616c6979756e2e4f54532e50726f74" .
            "6f4275666665722e50726f746f636f6c2e436f6d7075746553706c697450" .
            "6f696e7473427953697a65526573706f6e73652e53706c69744c6f636174" .
            "696f6e1a310a0d53706c69744c6f636174696f6e12100a086c6f63617469" .
            "6f6e180120022809120e0a0672657065617418022002281222650a0f5351" .
            "4c517565727952657175657374120d0a0571756572791801200228091243" .
            "0a0776657273696f6e18022001280e32322e616c6979756e2e4f54532e50" .
            "726f746f4275666665722e50726f746f636f6c2e53514c5061796c6f6164" .
            "56657273696f6e22c2010a155461626c65436f6e73756d65644361706163" .
            "69747912120a0a7461626c655f6e616d6518012001280912430a08636f6e" .
            "73756d656418022001280b32312e616c6979756e2e4f54532e50726f746f" .
            "4275666665722e50726f746f636f6c2e436f6e73756d6564436170616369" .
            "747912500a1372657365727665645f7468726f7567687075741803200128" .
            "0b32332e616c6979756e2e4f54532e50726f746f4275666665722e50726f" .
            "746f636f6c2e52657365727665645468726f75676870757422d7010a1653" .
            "6561726368436f6e73756d6564436170616369747912120a0a7461626c65" .
            "5f6e616d6518012001280912120a0a696e6465785f6e616d651802200128" .
            "0912430a08636f6e73756d656418032001280b32312e616c6979756e2e4f" .
            "54532e50726f746f4275666665722e50726f746f636f6c2e436f6e73756d" .
            "6564436170616369747912500a1372657365727665645f7468726f756768" .
            "70757418042001280b32332e616c6979756e2e4f54532e50726f746f4275" .
            "666665722e50726f746f636f6c2e52657365727665645468726f75676870" .
            "757422c2020a1053514c5175657279526573706f6e736512480a08636f6e" .
            "73756d657318012003280b32362e616c6979756e2e4f54532e50726f746f" .
            "4275666665722e50726f746f636f6c2e5461626c65436f6e73756d656443" .
            "61706163697479120c0a04726f777318022001280c12430a077665727369" .
            "6f6e18032001280e32322e616c6979756e2e4f54532e50726f746f427566" .
            "6665722e50726f746f636f6c2e53514c5061796c6f616456657273696f6e" .
            "123f0a047479706518042001280e32312e616c6979756e2e4f54532e5072" .
            "6f746f4275666665722e50726f746f636f6c2e53514c53746174656d656e" .
            "745479706512500a0f7365617263685f636f6e73756d657318052003280b" .
            "32372e616c6979756e2e4f54532e50726f746f4275666665722e50726f74" .
            "6f636f6c2e536561726368436f6e73756d656443617061636974792a350a" .
            "0e5072696d6172794b657954797065120b0a07494e54454745521001120a" .
            "0a06535452494e471002120a0a0642494e41525910032a630a1144656669" .
            "6e6564436f6c756d6e54797065120f0a0b4443545f494e54454745521001" .
            "120e0a0a4443545f444f55424c451002120f0a0b4443545f424f4f4c4541" .
            "4e1003120e0a0a4443545f535452494e471004120c0a084443545f424c4f" .
            "4210072a260a105072696d6172794b65794f7074696f6e12120a0e415554" .
            "4f5f494e4352454d454e5410012a2e0a0f426c6f6f6d46696c7465725479" .
            "706512080a044e4f4e45100112080a0443454c4c100212070a03524f5710" .
            "032a400a0d44617461426c6f636b5479706512140a104442545f504c4149" .
            "4e5f425546464552100012190a154442545f53494d504c455f524f575f4d" .
            "415452495810012a1c0a0c436f6d707265737354797065120c0a08435054" .
            "5f4e4f4e4510002a3a0a0f496e6465785570646174654d6f646512130a0f" .
            "49554d5f4153594e435f494e444558100012120a0e49554d5f53594e435f" .
            "494e44455810012a340a09496e6465785479706512130a0f49545f474c4f" .
            "42414c5f494e444558100012120a0e49545f4c4f43414c5f494e44455810" .
            "012a510a0b5461626c65537461747573120a0a064143544956451001120c" .
            "0a08494e4143544956451002120b0a074c4f4144494e471003120d0a0955" .
            "4e4c4f4144494e471004120c0a085550444154494e4710052a4d0a17526f" .
            "774578697374656e63654578706563746174696f6e120a0a0649474e4f52" .
            "45100012100a0c4558504543545f4558495354100112140a104558504543" .
            "545f4e4f545f455849535410022a390a0a52657475726e54797065120b0a" .
            "0752545f4e4f4e45100012090a0552545f504b100112130a0f52545f4146" .
            "5445525f4d4f4449465910022a300a0d4f7065726174696f6e5479706512" .
            "070a035055541001120a0a065550444154451002120a0a0644454c455445" .
            "10032a260a09446972656374696f6e120b0a07464f52574152441000120c" .
            "0a084241434b5741524410012a360a0c53747265616d5374617475731213" .
            "0a0f53545245414d5f454e41424c494e47100112110a0d53545245414d5f" .
            "41435449564510022a390a0a416374696f6e54797065120b0a075055545f" .
            "524f571001120e0a0a5550444154455f524f571002120e0a0a44454c4554" .
            "455f524f5710032a3f0a1153514c5061796c6f616456657273696f6e1214" .
            "0a1053514c5f504c41494e5f425546464552100112140a1053514c5f464c" .
            "41545f4255464645525310022a8d010a1053514c53746174656d656e7454" .
            "797065120e0a0a53514c5f53454c454354100112140a1053514c5f435245" .
            "4154455f5441424c45100212120a0e53514c5f53484f575f5441424c4510" .
            "0312160a1253514c5f44455343524942455f5441424c45100412120a0e53" .
            "514c5f44524f505f5441424c45100512130a0f53514c5f414c5445525f54" .
            "41424c451006"
        ));

        static::$is_initialized = true;
    }
}

